<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Doctor;
use Illuminate\Database\Seeder;

class DoctorSeeder extends Seeder
{
    public function run(): void
    {
        $doctors = [
            [
                'name' => 'dr. Andika Pratama, Sp.PD',
                'specialization' => 'Penyakit Dalam',
                'polyclinic' => 'Poli Penyakit Dalam',
                'bio' => 'Dokter spesialis penyakit dalam dengan pengalaman 12 tahun.',
                'schedules' => [
                    ['day_of_week' => 1, 'start_time' => '08:00', 'end_time' => '12:00', 'room' => 'PD-1'],
                    ['day_of_week' => 3, 'start_time' => '08:00', 'end_time' => '12:00', 'room' => 'PD-1'],
                    ['day_of_week' => 5, 'start_time' => '13:00', 'end_time' => '17:00', 'room' => 'PD-1'],
                ],
            ],
            [
                'name' => 'dr. Bunga Sari, Sp.A',
                'specialization' => 'Anak',
                'polyclinic' => 'Poli Anak',
                'bio' => 'Spesialis anak, fokus tumbuh kembang dan imunisasi.',
                'schedules' => [
                    ['day_of_week' => 1, 'start_time' => '09:00', 'end_time' => '13:00', 'room' => 'A-2'],
                    ['day_of_week' => 2, 'start_time' => '09:00', 'end_time' => '13:00', 'room' => 'A-2'],
                    ['day_of_week' => 4, 'start_time' => '14:00', 'end_time' => '18:00', 'room' => 'A-2'],
                ],
            ],
            [
                'name' => 'dr. Citra Widya, Sp.OG',
                'specialization' => 'Obstetri & Ginekologi',
                'polyclinic' => 'Poli Kebidanan',
                'bio' => 'Spesialis kandungan, USG 4D, persalinan normal & sectio.',
                'schedules' => [
                    ['day_of_week' => 2, 'start_time' => '08:00', 'end_time' => '12:00', 'room' => 'OG-1'],
                    ['day_of_week' => 4, 'start_time' => '08:00', 'end_time' => '12:00', 'room' => 'OG-1'],
                    ['day_of_week' => 6, 'start_time' => '09:00', 'end_time' => '13:00', 'room' => 'OG-1'],
                ],
            ],
            [
                'name' => 'dr. Dimas Kurniawan, Sp.JP',
                'specialization' => 'Jantung & Pembuluh Darah',
                'polyclinic' => 'Poli Jantung',
                'bio' => 'Spesialis jantung dengan layanan EKG, treadmill, echocardiography.',
                'schedules' => [
                    ['day_of_week' => 1, 'start_time' => '13:00', 'end_time' => '17:00', 'room' => 'JP-3'],
                    ['day_of_week' => 3, 'start_time' => '13:00', 'end_time' => '17:00', 'room' => 'JP-3'],
                    ['day_of_week' => 5, 'start_time' => '08:00', 'end_time' => '12:00', 'room' => 'JP-3'],
                ],
            ],
            [
                'name' => 'drg. Erlangga Putra',
                'specialization' => 'Gigi Umum',
                'polyclinic' => 'Poli Gigi',
                'bio' => 'Dokter gigi umum, fokus pada perawatan dasar dan estetika.',
                'schedules' => [
                    ['day_of_week' => 1, 'start_time' => '08:00', 'end_time' => '12:00', 'room' => 'G-1'],
                    ['day_of_week' => 2, 'start_time' => '08:00', 'end_time' => '12:00', 'room' => 'G-1'],
                    ['day_of_week' => 4, 'start_time' => '13:00', 'end_time' => '17:00', 'room' => 'G-1'],
                ],
            ],
            [
                'name' => 'dr. Fajar Nugroho, Sp.S',
                'specialization' => 'Saraf',
                'polyclinic' => 'Poli Saraf',
                'bio' => 'Spesialis saraf, layanan EEG dan EMG tersedia.',
                'leave_start_date' => now()->subDay()->toDateString(),
                'leave_end_date' => now()->addDays(5)->toDateString(),
                'leave_reason' => 'Cuti seminar internasional',
                'schedules' => [
                    ['day_of_week' => 2, 'start_time' => '13:00', 'end_time' => '17:00', 'room' => 'S-1'],
                    ['day_of_week' => 4, 'start_time' => '13:00', 'end_time' => '17:00', 'room' => 'S-1'],
                ],
            ],
        ];

        foreach ($doctors as $data) {
            $schedules = $data['schedules'];
            unset($data['schedules']);

            $doctor = Doctor::query()->updateOrCreate(
                ['name' => $data['name']],
                $data + ['is_active' => true],
            );

            $doctor->schedules()->delete();
            foreach ($schedules as $schedule) {
                $doctor->schedules()->create($schedule + ['is_active' => true]);
            }
        }
    }
}
