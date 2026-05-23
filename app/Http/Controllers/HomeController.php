<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\ChatbotSetting;
use App\Models\Doctor;
use App\Models\DoctorSchedule;
use App\Models\Faq;
use App\Models\FaqCategory;
use Inertia\Inertia;
use Inertia\Response;

class HomeController extends Controller
{
    public function __invoke(): Response
    {
        $hospitalName = ChatbotSetting::get('hospital_name', 'Rumah Sakit Ibnu Sina YW-UMI Makassar');

        $doctors = Doctor::query()
            ->with(['schedules' => fn ($q) => $q->where('is_active', true)
                ->orderBy('day_of_week')->orderBy('start_time')])
            ->where('is_active', true)
            ->orderBy('name')
            ->limit(6)
            ->get(['id', 'name', 'specialization', 'polyclinic', 'leave_start_date', 'leave_end_date'])
            ->map(fn (Doctor $d): array => [
                'id' => $d->id,
                'name' => $d->name,
                'specialization' => $d->specialization,
                'polyclinic' => $d->polyclinic,
                'on_leave' => $d->isOnLeave(),
                'schedule_summary' => $d->schedules->take(3)->map(fn ($s): string => substr(
                    DoctorSchedule::DAYS[$s->day_of_week] ?? '',
                    0,
                    3
                ).' '.substr((string) $s->start_time, 0, 5))->implode(' · '),
            ])->all();

        $specializations = Doctor::query()
            ->where('is_active', true)
            ->select('specialization')
            ->distinct()
            ->orderBy('specialization')
            ->pluck('specialization')
            ->all();

        $featuredFaqs = Faq::query()
            ->with('category:id,name')
            ->where('is_active', true)
            ->orderByDesc('priority')
            ->limit(6)
            ->get(['id', 'category_id', 'question', 'answer'])
            ->map(fn (Faq $f): array => [
                'id' => $f->id,
                'category' => $f->category?->name ?? '-',
                'question' => $f->question,
                'answer_preview' => mb_strlen($f->answer) > 140 ? mb_substr($f->answer, 0, 140).'…' : $f->answer,
            ])->all();

        $stats = [
            'doctors' => Doctor::query()->where('is_active', true)->count(),
            'specializations' => count($specializations),
            'faqs' => Faq::query()->where('is_active', true)->count(),
            'categories' => FaqCategory::query()->where('is_active', true)->count(),
        ];

        return Inertia::render('Landing', [
            'hospitalName' => $hospitalName,
            'doctors' => $doctors,
            'specializations' => $specializations,
            'featuredFaqs' => $featuredFaqs,
            'stats' => $stats,
            'contact' => [
                'address' => 'Jl. Urip Sumoharjo Km. 5 No. 264, Panaikang, Panakkukang, Makassar 90231',
                'phone' => '(0411) 452917',
                'igd' => '(0411) 452-917 ext. 119',
                'whatsapp' => '0822-9000-9119',
                'email' => 'cs@rsibnusina-ywumi.co.id',
                'website' => 'www.rsibnusina-ywumi.co.id',
            ],
        ]);
    }
}
