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
            [
                'category' => 'Umum',
                'question' => 'Apa saja jam operasional rumah sakit?',
                'answer' => "Jam operasional layanan:\n• IGD: 24 jam non-stop\n• Poliklinik: Senin–Sabtu 08.00–20.00, Minggu 09.00–14.00 (ada layanan terbatas)\n• Farmasi: 24 jam\n• Laboratorium & Radiologi: Senin–Sabtu 07.00–21.00, Minggu 08.00–14.00\n• Loket pendaftaran: Senin–Sabtu 07.00–20.00\n• Kasir: 24 jam (sesuai layanan terkait)",
                'keywords' => ['jam operasional', 'jam buka', 'jam layanan', 'jam praktek', 'jam kerja', 'buka', 'tutup', 'operasional'],
                'priority' => 80,
            ],
            [
                'category' => 'IGD',
                'question' => 'Bagaimana alur masuk IGD non-darurat?',
                'answer' => "IGD RS Sehat Sentosa buka 24 jam di gedung utama lantai dasar (pintu samping). Untuk pasien BPJS yang masuk via IGD tidak perlu rujukan faskes 1, cukup tunjukkan kartu BPJS aktif + KTP. Untuk pasien umum, deposit awal dapat diatur setelah pemeriksaan awal.\n\nUntuk kondisi mengancam nyawa, datang langsung ke IGD atau hubungi (021) 555-9119 / 119.",
                'keywords' => ['igd', 'gawat darurat', 'instalasi gawat darurat', 'unit gawat darurat', 'ugd', 'emergency room'],
                'priority' => 80,
            ],
            [
                'category' => 'Biaya',
                'question' => 'Bagaimana cara mengetahui estimasi biaya pengobatan?',
                'answer' => "Estimasi biaya dapat diperoleh melalui:\n1. Bagian Admisi (lantai 1) untuk rawat inap & tindakan terjadwal\n2. Customer Service (021) 555-1234 untuk pertanyaan tarif konsultasi & pemeriksaan umum\n3. Website rsss.example/tarif untuk daftar tarif tindakan standar\n\nUntuk pasien BPJS, sebagian besar layanan ditanggung sesuai paket INA-CBG. Pasien umum/asuransi swasta akan diberi rincian biaya sebelum tindakan.",
                'keywords' => ['biaya', 'tarif', 'harga', 'estimasi', 'rincian biaya', 'cost'],
                'priority' => 85,
            ],
            [
                'category' => 'Administrasi',
                'question' => 'Bagaimana cara mengambil hasil pemeriksaan lab/radiologi?',
                'answer' => "Pengambilan hasil pemeriksaan:\n1. Datang ke loket Laboratorium/Radiologi (lantai 2) dengan membawa bukti pembayaran/struk pemeriksaan dan KTP pasien\n2. Hasil dapat diwakilkan dengan surat kuasa bermaterai + KTP pasien dan KTP pengambil\n3. Hasil digital dapat diunduh di aplikasi mobile RS Sehat Sentosa setelah dikonfirmasi dokter\n\nUntuk interpretasi hasil pemeriksaan, silakan konsultasikan langsung dengan dokter pemeriksa atau dokter rujukan Anda.",
                'keywords' => ['ambil hasil', 'pengambilan hasil', 'hasil lab', 'hasil radiologi', 'hasil pemeriksaan', 'rontgen', 'usg', 'ct scan', 'mri'],
                'priority' => 75,
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
