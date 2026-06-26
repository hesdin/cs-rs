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
        $welcomeMessage = ChatbotSetting::get(
            'welcome_message',
            'Assalamualaikum, saya asisten virtual RS Ibnu Sina Makassar. Saya bisa bantu informasi jadwal dokter, pendaftaran, BPJS, dan administrasi rumah sakit. Ada yang bisa saya bantu?'
        );

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

        $chatCategories = FaqCategory::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get(['name', 'description'])
            ->map(fn (FaqCategory $category): array => [
                'name' => $category->name,
                'description' => $category->description,
            ])
            ->all();

        $chatSuggestions = [
            ['icon' => 'calendar', 'label' => 'Jadwal dokter spesialis anak hari ini'],
            ['icon' => 'clipboard', 'label' => 'Bagaimana cara mendaftar berobat?'],
            ['icon' => 'shield', 'label' => 'Apakah RS menerima BPJS?'],
            ['icon' => 'wallet', 'label' => 'Berapa biaya konsultasi dokter?'],
            ['icon' => 'bed', 'label' => 'Kelas kamar rawat inap apa saja?'],
            ['icon' => 'file', 'label' => 'Cara meminta surat keterangan sakit'],
        ];

        return Inertia::render('Landing', [
            'hospitalName' => $hospitalName,
            'welcomeMessage' => $welcomeMessage,
            'doctors' => $doctors,
            'specializations' => $specializations,
            'featuredFaqs' => $featuredFaqs,
            'chatCategories' => $chatCategories,
            'chatSuggestions' => $chatSuggestions,
            'stats' => $stats,
            'contact' => [
                'address' => 'Jl. Urip Sumoharjo Km. 5 No. 264, Panaikang, Panakkukang, Makassar 90231',
                'phone' => '(0411) 452917',
                'igd' => '(0411) 452-917 ext. 119',
                'whatsapp' => '0822-9000-9119',
                'email' => 'cs@rsibnusina-ywumi.co.id',
                'website' => 'www.rsibnusina-ywumi.co.id',
            ],
            'testimonials' => [
                [
                    'name' => 'Ibu Hasnawati',
                    'role' => 'Pasien Poli Penyakit Dalam',
                    'message' => 'Perawat ramah, dokter sabar menjelaskan kondisi saya. Suasana rumah sakit nyaman dan bersih, mushola juga tersedia di setiap lantai.',
                    'rating' => 5,
                ],
                [
                    'name' => 'Bapak Andi Tahir',
                    'role' => 'Keluarga Pasien Rawat Inap',
                    'message' => 'Daftar lewat WhatsApp gampang sekali, tinggal kirim KTP & rujukan BPJS. Antrean rapi, panggilan via aplikasi memudahkan kami yang menunggu.',
                    'rating' => 5,
                    'featured' => true,
                ],
                [
                    'name' => 'Ibu Siti Aminah',
                    'role' => 'Pasien Poli Kebidanan',
                    'message' => 'Persalinan saya dibantu dengan baik. Kamar VIP bersih, perawat sigap. Suami bisa pantau jadwal kontrol bayi via aplikasi, sangat membantu.',
                    'rating' => 5,
                ],
            ],
            'insights' => [
                [
                    'title' => '5 Tips Menjaga Kesehatan Jantung di Usia 40+',
                    'excerpt' => 'Panduan praktis menjaga jantung tetap sehat: pola makan, aktivitas fisik, dan kapan sebaiknya cek ke dokter spesialis jantung.',
                    'date' => '15 Mei 2026',
                    'category' => 'Tips Kesehatan',
                    'image' => 'https://images.unsplash.com/photo-1571902943202-507ec2618e8f?w=600&auto=format&fit=crop&q=70',
                    'reading_time' => 5,
                    'author' => 'Tim Medis',
                ],
                [
                    'title' => 'Manfaat Medical Check-Up Berkala bagi Keluarga',
                    'excerpt' => 'MCU rutin membantu deteksi dini diabetes, hipertensi, dan kolesterol — sebelum gejala muncul. Cek paket MCU yang sesuai untuk Anda.',
                    'date' => '10 Mei 2026',
                    'category' => 'Medical Check-Up',
                    'image' => 'https://images.unsplash.com/photo-1666214280165-1c5bdc58e9b6?w=600&auto=format&fit=crop&q=70',
                    'reading_time' => 4,
                    'author' => 'Dr. Andi Mappangara',
                ],
                [
                    'title' => 'Persiapan Sebelum Berangkat Haji & Umroh',
                    'excerpt' => 'Vaksin meningitis, MCU khusus jamaah, dan tips menjaga kebugaran selama ibadah. Layanan terpadu dari RS Ibnu Sina.',
                    'date' => '5 Mei 2026',
                    'category' => 'Haji & Umroh',
                    'image' => 'https://images.unsplash.com/photo-1542816417-0983c9c9ad53?w=600&auto=format&fit=crop&q=70',
                    'reading_time' => 6,
                    'author' => 'Tim Poli Vaksinasi',
                ],
            ],
        ]);
    }
}
