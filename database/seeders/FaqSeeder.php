<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Faq;
use Illuminate\Database\Seeder;

class FaqSeeder extends Seeder
{
    public function run(): void
    {
        $faqs = [
            [
                'category' => 'Umum',
                'question' => 'Di mana lokasi Rumah Sakit Sehat Sentosa?',
                'answer' => 'RS Sehat Sentosa berada di Jl. Melati No. 1, Jakarta Pusat. Lihat denah lengkap di website kami.',
                'keywords' => ['lokasi', 'alamat', 'denah'],
                'priority' => 90,
            ],
            [
                'category' => 'Umum',
                'question' => 'Berapa nomor telepon RS Sehat Sentosa?',
                'answer' => 'Customer service: (021) 555-1234. Untuk gawat darurat hubungi IGD: (021) 555-9119 atau 119.',
                'keywords' => ['telepon', 'kontak', 'cs', 'customer service'],
                'priority' => 90,
            ],
            [
                'category' => 'Umum',
                'question' => 'Apa saja jam besuk pasien?',
                'answer' => 'Jam besuk: Senin–Jumat pukul 11.00–13.00 dan 17.00–20.00. Sabtu–Minggu pukul 10.00–20.00. Maksimal 2 pengunjung per pasien.',
                'keywords' => ['jam besuk', 'visit', 'kunjungan', 'pengunjung'],
                'priority' => 70,
            ],
            [
                'category' => 'Pendaftaran',
                'question' => 'Bagaimana cara mendaftar berobat?',
                'answer' => 'Pendaftaran dapat dilakukan: (1) Online via website rsss.example/daftar, (2) Aplikasi mobile, (3) Datang langsung ke loket pendaftaran lantai 1. Bawa KTP dan kartu BPJS/asuransi jika ada.',
                'keywords' => ['daftar', 'pendaftaran', 'registrasi'],
                'priority' => 100,
            ],
            [
                'category' => 'Pendaftaran',
                'question' => 'Apakah RS menerima BPJS Kesehatan?',
                'answer' => 'Ya. RS Sehat Sentosa adalah faskes rujukan tingkat lanjut BPJS. Pastikan Anda membawa kartu BPJS aktif dan surat rujukan dari faskes 1 (kecuali untuk IGD).',
                'keywords' => ['bpjs', 'asuransi', 'jaminan', 'rujukan'],
                'priority' => 100,
            ],
            [
                'category' => 'Administrasi',
                'question' => 'Bagaimana prosedur rawat inap?',
                'answer' => 'Setelah dokter menyatakan perlu rawat inap: (1) Konfirmasi kelas kamar di bagian admisi, (2) Tanda tangan persetujuan, (3) Deposit (jika umum) atau verifikasi penjamin (BPJS/asuransi). Estimasi tarif tersedia di admisi.',
                'keywords' => ['rawat inap', 'opname', 'kamar', 'admisi'],
                'priority' => 80,
            ],
            [
                'category' => 'Administrasi',
                'question' => 'Bagaimana cara membuat janji temu dengan dokter?',
                'answer' => 'Buat janji temu via aplikasi mobile atau website rsss.example/appointment. Pilih poliklinik, dokter, dan jadwal yang tersedia. Anda akan menerima kode antrian via SMS/email.',
                'keywords' => ['janji', 'appointment', 'booking', 'reservasi'],
                'priority' => 90,
            ],
            [
                'category' => 'Fasilitas',
                'question' => 'Apa saja fasilitas yang tersedia?',
                'answer' => 'Fasilitas meliputi IGD 24 jam, ICU/NICU/PICU, Laboratorium, Radiologi (X-Ray, CT Scan, MRI, USG), Farmasi 24 jam, Kantin, ATM, Mushola, dan area parkir luas.',
                'keywords' => ['fasilitas', 'igd', 'icu', 'lab', 'radiologi'],
                'priority' => 60,
            ],
        ];

        foreach ($faqs as $faq) {
            Faq::query()->updateOrCreate(
                ['question' => $faq['question']],
                $faq + ['is_active' => true],
            );
        }
    }
}
