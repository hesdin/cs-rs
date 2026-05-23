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
                'name' => 'dr. Andi Mappangara, Sp.PD',
                'specialization' => 'Penyakit Dalam',
                'polyclinic' => 'Poli Penyakit Dalam',
                'bio' => 'Dokter spesialis penyakit dalam dengan pengalaman 14 tahun, fokus pada diabetes, hipertensi, dan gangguan pencernaan.',
                'schedules' => [
                    ['day_of_week' => 1, 'start_time' => '08:00', 'end_time' => '12:00', 'room' => 'PD-1'],
                    ['day_of_week' => 3, 'start_time' => '08:00', 'end_time' => '12:00', 'room' => 'PD-1'],
                    ['day_of_week' => 5, 'start_time' => '14:00', 'end_time' => '17:00', 'room' => 'PD-1'],
                ],
            ],
            [
                'name' => 'dr. Bunga Citra Pratiwi, Sp.A',
                'specialization' => 'Anak',
                'polyclinic' => 'Poli Anak',
                'bio' => 'Spesialis anak, fokus pada tumbuh kembang, alergi, dan imunisasi sesuai jadwal IDAI.',
                'schedules' => [
                    ['day_of_week' => 1, 'start_time' => '09:00', 'end_time' => '13:00', 'room' => 'A-2'],
                    ['day_of_week' => 2, 'start_time' => '09:00', 'end_time' => '13:00', 'room' => 'A-2'],
                    ['day_of_week' => 4, 'start_time' => '15:00', 'end_time' => '18:00', 'room' => 'A-2'],
                ],
            ],
            [
                'name' => 'dr. Aisyah Rahman, Sp.OG',
                'specialization' => 'Obstetri & Ginekologi',
                'polyclinic' => 'Poli Kebidanan',
                'bio' => 'Spesialis kandungan dengan layanan USG 4D, persalinan normal & sectio, serta program kehamilan.',
                'schedules' => [
                    ['day_of_week' => 2, 'start_time' => '08:00', 'end_time' => '12:00', 'room' => 'OG-1'],
                    ['day_of_week' => 4, 'start_time' => '08:00', 'end_time' => '12:00', 'room' => 'OG-1'],
                    ['day_of_week' => 6, 'start_time' => '09:00', 'end_time' => '13:00', 'room' => 'OG-1'],
                ],
            ],
            [
                'name' => 'dr. Muhammad Yusran Anwar, Sp.JP',
                'specialization' => 'Jantung & Pembuluh Darah',
                'polyclinic' => 'Poli Jantung',
                'bio' => 'Spesialis jantung dengan layanan EKG, treadmill test, echocardiography, dan kateterisasi.',
                'schedules' => [
                    ['day_of_week' => 1, 'start_time' => '14:00', 'end_time' => '17:00', 'room' => 'JP-3'],
                    ['day_of_week' => 3, 'start_time' => '14:00', 'end_time' => '17:00', 'room' => 'JP-3'],
                    ['day_of_week' => 5, 'start_time' => '08:00', 'end_time' => '12:00', 'room' => 'JP-3'],
                ],
            ],
            [
                'name' => 'dr. Hartono Wijaya, Sp.B',
                'specialization' => 'Bedah Umum',
                'polyclinic' => 'Poli Bedah',
                'bio' => 'Spesialis bedah umum, fokus bedah digestif, hernia, dan kasus laparoskopi minimal invasif.',
                'schedules' => [
                    ['day_of_week' => 1, 'start_time' => '13:00', 'end_time' => '16:00', 'room' => 'B-2'],
                    ['day_of_week' => 4, 'start_time' => '08:00', 'end_time' => '12:00', 'room' => 'B-2'],
                ],
            ],
            [
                'name' => 'drg. Erlangga Pratama Putra',
                'specialization' => 'Gigi Umum',
                'polyclinic' => 'Poli Gigi',
                'bio' => 'Dokter gigi umum, fokus pada perawatan dasar, scaling, dan estetika gigi.',
                'schedules' => [
                    ['day_of_week' => 1, 'start_time' => '08:00', 'end_time' => '12:00', 'room' => 'G-1'],
                    ['day_of_week' => 2, 'start_time' => '08:00', 'end_time' => '12:00', 'room' => 'G-1'],
                    ['day_of_week' => 4, 'start_time' => '14:00', 'end_time' => '17:00', 'room' => 'G-1'],
                ],
            ],
            [
                'name' => 'dr. Fajar Nugroho Hidayat, Sp.S',
                'specialization' => 'Saraf',
                'polyclinic' => 'Poli Saraf',
                'bio' => 'Spesialis saraf, layanan EEG, EMG, dan tatalaksana stroke serta nyeri kepala.',
                'leave_start_date' => now()->subDay()->toDateString(),
                'leave_end_date' => now()->addDays(5)->toDateString(),
                'leave_reason' => 'Cuti seminar internasional',
                'schedules' => [
                    ['day_of_week' => 2, 'start_time' => '14:00', 'end_time' => '17:00', 'room' => 'S-1'],
                    ['day_of_week' => 4, 'start_time' => '14:00', 'end_time' => '17:00', 'room' => 'S-1'],
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
