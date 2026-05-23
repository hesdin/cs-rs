<?php

declare(strict_types=1);

namespace App\Actions\Chatbot;

use App\Models\Doctor;
use App\Models\Faq;
use Illuminate\Database\Eloquent\Builder;

/**
 * RAG sederhana: ambil FAQ + jadwal dokter yang relevan untuk pertanyaan user
 * berdasarkan keyword match (PostgreSQL ILIKE).
 *
 * @phpstan-type FaqArray array{id:int, category:string, question:string, answer:string}
 * @phpstan-type DoctorArray array{
 *     id:int,
 *     name:string,
 *     specialization:string,
 *     polyclinic:?string,
 *     on_leave:bool,
 *     leave_period:?string,
 *     leave_reason:?string,
 *     schedules: list<array{day:string, start:string, end:string, room:?string}>
 * }
 */
class RetrieveKnowledge
{
    /**
     * @return array{faqs: list<FaqArray>, doctors: list<DoctorArray>}
     */
    public function __invoke(string $message): array
    {
        $tokens = $this->tokenize($message);

        return [
            'faqs' => $this->findFaqs($tokens),
            'doctors' => $this->findDoctors($tokens),
        ];
    }

    /**
     * @return list<string>
     */
    private function tokenize(string $message): array
    {
        $clean = mb_strtolower($message);
        $clean = preg_replace('/[^\p{L}\p{N}\s]+/u', ' ', $clean) ?? '';
        $words = preg_split('/\s+/u', trim($clean)) ?: [];

        // Buang stopwords pendek & umum.
        $stopwords = ['di', 'ke', 'dari', 'untuk', 'yang', 'dan', 'atau',
            'apa', 'apakah', 'bagaimana', 'kapan', 'dimana', 'siapa',
            'saya', 'kami', 'kita', 'anda', 'kamu', 'ada', 'jam',
            'hari', 'tanggal', 'mau', 'ingin', 'bisa', 'dengan'];

        return array_values(array_filter(
            $words,
            fn (string $w): bool => mb_strlen($w) >= 3 && ! in_array($w, $stopwords, true)
        ));
    }

    /**
     * @param  list<string>  $tokens
     * @return list<FaqArray>
     */
    private function findFaqs(array $tokens): array
    {
        if ($tokens === []) {
            return [];
        }

        $limit = (int) config('openrouter.chatbot.max_faq_context', 5);

        $faqs = Faq::query()
            ->where('is_active', true)
            ->where(function (Builder $q) use ($tokens): void {
                foreach ($tokens as $token) {
                    $like = '%'.mb_strtolower($token).'%';
                    $q->orWhereRaw('LOWER(question) LIKE ?', [$like])
                        ->orWhereRaw('LOWER(answer) LIKE ?', [$like])
                        ->orWhereRaw('LOWER(category) LIKE ?', [$like])
                        ->orWhereRaw('LOWER(CAST(keywords AS TEXT)) LIKE ?', [$like]);
                }
            })
            ->orderByDesc('priority')
            ->limit($limit)
            ->get(['id', 'category', 'question', 'answer']);

        return $faqs->map(fn (Faq $f): array => [
            'id' => $f->id,
            'category' => $f->category,
            'question' => $f->question,
            'answer' => $f->answer,
        ])->all();
    }

    /**
     * @param  list<string>  $tokens
     * @return list<DoctorArray>
     */
    private function findDoctors(array $tokens): array
    {
        if ($tokens === []) {
            return [];
        }

        $limit = (int) config('openrouter.chatbot.max_schedule_context', 10);

        $doctors = Doctor::query()
            ->with(['schedules' => fn ($q) => $q->where('is_active', true)
                ->orderBy('day_of_week')->orderBy('start_time')])
            ->where('is_active', true)
            ->where(function (Builder $q) use ($tokens): void {
                foreach ($tokens as $token) {
                    $like = '%'.mb_strtolower($token).'%';
                    $q->orWhereRaw('LOWER(name) LIKE ?', [$like])
                        ->orWhereRaw('LOWER(specialization) LIKE ?', [$like])
                        ->orWhereRaw('LOWER(COALESCE(polyclinic, \'\')) LIKE ?', [$like]);
                }
            })
            ->limit($limit)
            ->get();

        return $doctors->map(fn (Doctor $d): array => [
            'id' => $d->id,
            'name' => $d->name,
            'specialization' => $d->specialization,
            'polyclinic' => $d->polyclinic,
            'on_leave' => $d->isOnLeave(),
            'leave_period' => ($d->leave_start_date && $d->leave_end_date)
                ? $d->leave_start_date->isoFormat('D MMM YYYY').' – '.$d->leave_end_date->isoFormat('D MMM YYYY')
                : null,
            'leave_reason' => $d->leave_reason,
            'schedules' => $d->schedules->map(fn ($s): array => [
                'day' => $s->dayName(),
                'start' => substr((string) $s->start_time, 0, 5),
                'end' => substr((string) $s->end_time, 0, 5),
                'room' => $s->room,
            ])->all(),
        ])->all();
    }
}
