<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Faq;
use Illuminate\Database\Seeder;

class FaqSeeder extends Seeder
{
    public function run(): void
    {
        foreach ($this->faqs() as $faq) {
            Faq::query()->updateOrCreate(
                ['question' => $faq['question']],
                $faq + ['is_active' => true],
            );
        }
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function faqs(): array
    {
        return [
            // ============================================================
            // UMUM & KONTAK
            // ============================================================
            [
                'category' => 'Umum',
                'question' => 'Di mana lokasi Rumah Sakit Sehat Sentosa?',
                'answer' => 'RS Sehat Sentosa berada di Jl. Melati No. 1, Jakarta Pusat. Pintu utama menghadap jalan raya, akses dekat dengan halte bus dan stasiun MRT.',
                'keywords' => ['lokasi', 'alamat', 'denah', 'dimana', 'di mana'],
                'priority' => 90,
            ],
            [
                'category' => 'Umum',
                'question' => 'Berapa nomor telepon RS Sehat Sentosa?',
                'answer' => "Kontak resmi RS Sehat Sentosa:\n• Customer Service: (021) 555-1234\n• IGD 24 jam: (021) 555-9119\n• Layanan darurat nasional: 119\n• Email: cs@rsss.example\n• WhatsApp CS: 0812-3456-7890",
                'keywords' => ['telepon', 'kontak', 'cs', 'customer service', 'nomor', 'whatsapp', 'email'],
                'priority' => 90,
            ],
            [
                'category' => 'Umum',
                'question' => 'Apa saja jam operasional rumah sakit?',
                'answer' => "Jam operasional layanan:\n• IGD: 24 jam non-stop\n• Poliklinik: Senin–Sabtu 08.00–20.00, Minggu 09.00–14.00 (terbatas)\n• Farmasi: 24 jam\n• Laboratorium & Radiologi: Senin–Sabtu 07.00–21.00, Minggu 08.00–14.00\n• Loket pendaftaran: Senin–Sabtu 07.00–20.00\n• Kasir: 24 jam (sesuai layanan terkait)",
                'keywords' => ['jam operasional', 'jam buka', 'jam layanan', 'jam praktek', 'jam kerja', 'buka', 'tutup', 'operasional'],
                'priority' => 90,
            ],
            [
                'category' => 'Umum',
                'question' => 'Apa saja jam besuk pasien?',
                'answer' => "Jam besuk pasien rawat inap:\n• Senin–Jumat: 11.00–13.00 dan 17.00–20.00\n• Sabtu–Minggu: 10.00–20.00\n• Maksimal 2 pengunjung per pasien\n• Anak di bawah 12 tahun tidak diperkenankan masuk ruang rawat\n• Wajib registrasi di pos satpam dan menggunakan masker",
                'keywords' => ['jam besuk', 'visit', 'kunjungan', 'pengunjung', 'jenguk', 'menjenguk'],
                'priority' => 75,
            ],

            // ============================================================
            // PENDAFTARAN & APPOINTMENT
            // ============================================================
            [
                'category' => 'Pendaftaran',
                'question' => 'Bagaimana cara daftar berobat?',
                'answer' => "Pendaftaran dapat dilakukan via:\n1. Online — website rsss.example/daftar (24 jam)\n2. Aplikasi mobile RS Sehat Sentosa (Android/iOS)\n3. WhatsApp CS 0812-3456-7890\n4. Loket pendaftaran lantai 1 (Senin–Sabtu 07.00–20.00)\n\nDokumen yang dibawa: KTP, kartu BPJS/asuransi (jika ada), surat rujukan faskes 1 (untuk BPJS rawat jalan).",
                'keywords' => ['daftar', 'pendaftaran', 'registrasi', 'berobat', 'cara daftar'],
                'priority' => 100,
            ],
            [
                'category' => 'Pendaftaran',
                'question' => 'Apakah bisa daftar online?',
                'answer' => "Ya. Pendaftaran online tersedia 24 jam:\n• Website: rsss.example/daftar\n• Aplikasi mobile: \"RS Sehat Sentosa\" di Play Store / App Store\n• WhatsApp CS: 0812-3456-7890\n\nAnda akan menerima nomor antrean via SMS/email. Datang 30 menit sebelum jadwal untuk verifikasi di loket.",
                'keywords' => ['daftar online', 'pendaftaran online', 'website', 'aplikasi', 'mobile', 'app'],
                'priority' => 100,
            ],
            [
                'category' => 'Pendaftaran',
                'question' => 'Bagaimana cara membuat janji temu dengan dokter?',
                'answer' => "Buat janji temu (appointment) via:\n1. Aplikasi mobile / website rsss.example/appointment\n2. WhatsApp CS\n3. Telepon CS (021) 555-1234\n\nLangkah: pilih poliklinik → pilih dokter → pilih jadwal yang tersedia → konfirmasi. Anda akan menerima kode antrian via SMS/email.",
                'keywords' => ['janji', 'janji temu', 'appointment', 'booking', 'reservasi', 'jadwal'],
                'priority' => 95,
            ],
            [
                'category' => 'Pendaftaran',
                'question' => 'Apakah bisa booking nomor antrean?',
                'answer' => 'Bisa. Nomor antrean otomatis diberikan saat Anda menyelesaikan pendaftaran online atau di loket. Anda dapat memantau urutan antrean secara real-time di aplikasi mobile RS Sehat Sentosa.',
                'keywords' => ['antrean', 'antrian', 'booking antrean', 'nomor antrian', 'queue'],
                'priority' => 80,
            ],
            [
                'category' => 'Pendaftaran',
                'question' => 'Berapa lama estimasi antreannya?',
                'answer' => 'Estimasi waktu antrean ditampilkan di aplikasi mobile dan website (real-time). Rata-rata waktu tunggu di poliklinik 30–90 menit tergantung dokter dan jumlah pasien. Untuk gambaran cepat, hubungi CS (021) 555-1234.',
                'keywords' => ['estimasi antrean', 'lama antrean', 'waktu tunggu', 'tunggu', 'antri berapa lama'],
                'priority' => 70,
            ],

            // ============================================================
            // DOKTER & POLIKLINIK
            // ============================================================
            [
                'category' => 'Dokter',
                'question' => 'Spesialis apa saja yang tersedia?',
                'answer' => "Saat ini tersedia spesialis:\n• Penyakit Dalam (Sp.PD)\n• Anak (Sp.A)\n• Obstetri & Ginekologi / Kandungan (Sp.OG)\n• Jantung & Pembuluh Darah (Sp.JP)\n• Saraf (Sp.S)\n• Gigi Umum\n\nUntuk daftar dokter terbaru dan jadwal harian, gunakan fitur pencarian dokter di aplikasi atau tanya saya jadwal dokter spesifik.",
                'keywords' => ['spesialis', 'spesialisasi', 'dokter apa saja', 'poliklinik', 'poli'],
                'priority' => 90,
            ],
            [
                'category' => 'Dokter',
                'question' => 'Bagaimana mengetahui dokter sedang praktik atau cuti?',
                'answer' => 'Status praktik / cuti dokter dapat dicek real-time di aplikasi mobile dan website pada halaman pencarian dokter. Tanya saya nama dokter yang dimaksud, saya akan beri tahu status terbarunya.',
                'keywords' => ['cuti', 'libur dokter', 'praktik', 'tidak praktik', 'sedang praktek'],
                'priority' => 80,
            ],

            // ============================================================
            // BIAYA & PEMBAYARAN
            // ============================================================
            [
                'category' => 'Biaya',
                'question' => 'Bagaimana cara mengetahui estimasi biaya pengobatan?',
                'answer' => "Estimasi biaya:\n1. Bagian Admisi (lantai 1) — untuk rawat inap & tindakan terjadwal\n2. CS (021) 555-1234 — tarif konsultasi & pemeriksaan umum\n3. Website rsss.example/tarif — daftar tarif tindakan standar\n\nUntuk pasien BPJS, sebagian besar layanan ditanggung sesuai paket INA-CBG. Pasien umum/asuransi swasta diberi rincian sebelum tindakan.",
                'keywords' => ['biaya', 'tarif', 'harga', 'estimasi biaya', 'rincian biaya', 'cost'],
                'priority' => 90,
            ],
            [
                'category' => 'Biaya',
                'question' => 'Berapa biaya konsultasi dokter?',
                'answer' => "Biaya konsultasi (di luar tindakan dan obat):\n• Dokter umum: Rp 150.000\n• Dokter spesialis: Rp 250.000–400.000 (tergantung spesialisasi)\n• Sub-spesialis / konsultan: Rp 400.000–600.000\n\nUntuk pasien BPJS rawat jalan dengan rujukan, konsultasi tidak dikenakan biaya tambahan. Tarif pasti dapat ditanyakan ke CS (021) 555-1234.",
                'keywords' => ['biaya konsultasi', 'tarif dokter', 'harga konsultasi', 'biaya periksa'],
                'priority' => 85,
            ],
            [
                'category' => 'Biaya',
                'question' => 'Bagaimana cara mengetahui biaya tindakan atau pemeriksaan tertentu?',
                'answer' => 'Untuk biaya tindakan/pemeriksaan spesifik (operasi, USG, CT scan, MRI, lab tertentu, dll.) silakan hubungi: CS (021) 555-1234 atau bagian Admisi. Petugas akan memberikan estimasi biaya tertulis sebelum tindakan dilakukan.',
                'keywords' => ['biaya tindakan', 'tarif pemeriksaan', 'biaya operasi', 'tarif lab', 'tarif radiologi'],
                'priority' => 80,
            ],
            [
                'category' => 'Biaya',
                'question' => 'Apakah bisa bayar dengan debit, QRIS, atau transfer?',
                'answer' => "Metode pembayaran yang diterima:\n• Tunai (kasir 24 jam)\n• Kartu debit (semua bank)\n• Kartu kredit (Visa, Mastercard, JCB)\n• QRIS (semua e-wallet & m-banking)\n• Transfer bank (BCA, Mandiri, BNI, BRI)\n• Virtual Account untuk pembayaran online",
                'keywords' => ['debit', 'qris', 'transfer', 'kartu kredit', 'bayar', 'pembayaran', 'cara bayar', 'metode pembayaran'],
                'priority' => 80,
            ],
            [
                'category' => 'Biaya',
                'question' => 'Apakah biaya rumah sakit bisa dicicil?',
                'answer' => 'Untuk pasien umum dengan biaya tindakan tertentu, tersedia opsi cicilan via kartu kredit (program 0% beberapa bank partner) atau cicilan langsung melalui bagian keuangan dengan persetujuan khusus. Hubungi bagian Keuangan / Admisi untuk detail dan persyaratan.',
                'keywords' => ['cicil', 'cicilan', 'angsuran', 'kredit', 'installment'],
                'priority' => 60,
            ],
            [
                'category' => 'Biaya',
                'question' => 'Berapa estimasi biaya rawat inap?',
                'answer' => "Estimasi biaya rawat inap (kamar saja, di luar tindakan & obat):\n• Kelas 3 / VIP: lihat di tabel di bawah\nKelas kamar tersedia di FAQ \"Kelas kamar apa saja yang tersedia?\". Untuk estimasi total (kamar + tindakan + obat), bagian Admisi akan memberikan rincian sebelum pasien masuk kamar.",
                'keywords' => ['biaya rawat inap', 'tarif rawat inap', 'biaya opname', 'estimasi rawat inap'],
                'priority' => 75,
            ],

            // ============================================================
            // BPJS & ASURANSI
            // ============================================================
            [
                'category' => 'BPJS',
                'question' => 'Apakah RS menerima BPJS Kesehatan?',
                'answer' => 'Ya. RS Sehat Sentosa adalah faskes rujukan tingkat lanjut BPJS Kesehatan. Pastikan kartu BPJS aktif dan membawa surat rujukan dari faskes 1 (kecuali untuk IGD).',
                'keywords' => ['bpjs', 'jaminan kesehatan', 'kartu bpjs'],
                'priority' => 100,
            ],
            [
                'category' => 'BPJS',
                'question' => 'Bagaimana alur berobat menggunakan BPJS?',
                'answer' => "Alur BPJS rawat jalan:\n1. Cek status BPJS aktif (aplikasi Mobile JKN)\n2. Datang ke faskes 1 (puskesmas/klinik) untuk minta surat rujukan\n3. Bawa rujukan + KTP + kartu BPJS ke loket BPJS RS\n4. Verifikasi & ambil nomor antrean poliklinik\n5. Konsultasi dokter\n\nUntuk IGD: BPJS langsung berlaku tanpa rujukan, tunjukkan kartu BPJS aktif + KTP saat registrasi.",
                'keywords' => ['alur bpjs', 'prosedur bpjs', 'cara bpjs', 'pakai bpjs'],
                'priority' => 95,
            ],
            [
                'category' => 'BPJS',
                'question' => 'Apakah perlu surat rujukan untuk berobat dengan BPJS?',
                'answer' => "Ya, untuk rawat jalan dengan BPJS WAJIB membawa surat rujukan dari faskes 1 (puskesmas/klinik tempat Anda terdaftar).\n\nPENGECUALIAN: tidak perlu rujukan untuk:\n• Kondisi gawat darurat (masuk via IGD)\n• Kontrol berkelanjutan dengan rujukan internal RS yang masih berlaku",
                'keywords' => ['rujukan', 'surat rujukan', 'faskes 1', 'rujuk', 'puskesmas'],
                'priority' => 85,
            ],
            [
                'category' => 'BPJS',
                'question' => 'Poli apa saja yang bisa menggunakan BPJS?',
                'answer' => 'Hampir semua poliklinik di RS Sehat Sentosa menerima BPJS dengan rujukan yang sesuai diagnosa: Penyakit Dalam, Anak, Kandungan, Jantung, Saraf, dan Gigi Umum. Beberapa tindakan estetika atau di luar indikasi medis tidak ditanggung BPJS.',
                'keywords' => ['poli bpjs', 'poliklinik bpjs', 'spesialis bpjs'],
                'priority' => 80,
            ],
            [
                'category' => 'Asuransi',
                'question' => 'Apakah asuransi saya bekerja sama dengan rumah sakit ini?',
                'answer' => "RS Sehat Sentosa bekerja sama dengan asuransi swasta utama, antara lain:\n• Allianz, AXA, Prudential, Manulife\n• Cigna, Sequis, MNC Life\n• Inhealth, Mandiri Inhealth\n• Lippo Insurance, Sinarmas MSIG\n\nUntuk asuransi yang tidak terdaftar, pembayaran reimbursement tetap dapat diproses (pasien bayar dulu, klaim ke asuransi). Hubungi CS untuk verifikasi terbaru.",
                'keywords' => ['asuransi', 'asuransi swasta', 'allianz', 'axa', 'prudential', 'manulife', 'cigna', 'kerja sama'],
                'priority' => 80,
            ],
            [
                'category' => 'Asuransi',
                'question' => 'Bagaimana proses klaim asuransi swasta?',
                'answer' => "Dua skema klaim:\n1. CASHLESS (provider asuransi terdaftar): tunjukkan kartu asuransi + KTP di loket admisi. Petugas akan verifikasi ke pihak asuransi sebelum tindakan.\n2. REIMBURSEMENT: pasien bayar lebih dulu, kemudian klaim ke asuransi dengan dokumen: kuitansi asli, resume medis, hasil pemeriksaan, dan formulir klaim asuransi (minta di bagian rekam medis).",
                'keywords' => ['klaim asuransi', 'cashless', 'reimbursement', 'reimburse', 'klaim'],
                'priority' => 75,
            ],

            // ============================================================
            // LAYANAN MEDIS
            // ============================================================
            [
                'category' => 'Layanan',
                'question' => 'Apakah ada IGD 24 jam?',
                'answer' => 'Ya. Instalasi Gawat Darurat (IGD) buka 24 jam non-stop, 7 hari seminggu. IGD berada di gedung utama lantai dasar (pintu samping). Untuk kondisi mengancam nyawa, datang langsung atau hubungi (021) 555-9119 / 119.',
                'keywords' => ['igd', 'gawat darurat', 'ugd', 'emergency room', '24 jam'],
                'priority' => 95,
            ],
            [
                'category' => 'Layanan',
                'question' => 'Apakah tersedia layanan laboratorium?',
                'answer' => 'Ya. Laboratorium klinik tersedia di lantai 2, melayani pemeriksaan darah lengkap, kimia darah, urinalisis, mikrobiologi, dan tes COVID-19. Buka Senin–Sabtu 07.00–21.00, Minggu 08.00–14.00. Hasil pemeriksaan rutin biasanya keluar 2–6 jam.',
                'keywords' => ['lab', 'laboratorium', 'cek darah', 'urin', 'tes lab'],
                'priority' => 80,
            ],
            [
                'category' => 'Layanan',
                'question' => 'Apakah tersedia rontgen, USG, CT scan, atau MRI?',
                'answer' => "Layanan radiologi & imaging:\n• X-Ray (Rontgen): tersedia 24 jam (via IGD atau rujukan)\n• USG (umum & 4D obstetri)\n• CT Scan multi-slice\n• MRI 1.5 Tesla\n• Mammografi\n• Fluoroskopi\n\nBeberapa pemeriksaan perlu janji terlebih dahulu. Hubungi bagian Radiologi atau CS untuk reservasi.",
                'keywords' => ['rontgen', 'x-ray', 'usg', 'ct scan', 'mri', 'mammografi', 'radiologi'],
                'priority' => 80,
            ],
            [
                'category' => 'Layanan',
                'question' => 'Apakah bisa medical check-up (MCU)?',
                'answer' => "Ya. Tersedia paket Medical Check-Up:\n• MCU Basic (Rp 750.000)\n• MCU Standard (Rp 1.500.000)\n• MCU Executive (Rp 3.500.000)\n• MCU Pre-Employment / Pre-Marital\n\nPaket meliputi pemeriksaan fisik, lab, EKG, rontgen thorax, dan konsultasi dokter. Wajib reservasi via CS atau aplikasi.",
                'keywords' => ['mcu', 'medical check up', 'medical check-up', 'cek kesehatan', 'general check up'],
                'priority' => 80,
            ],
            [
                'category' => 'Layanan',
                'question' => 'Apakah ada layanan vaksin?',
                'answer' => "Ya. Layanan vaksinasi tersedia:\n• Vaksin anak (sesuai jadwal IDAI): BCG, DPT, polio, MMR, dll.\n• Vaksin dewasa: influenza, hepatitis A/B, HPV, tetanus, varicella, pneumonia\n• Vaksin perjalanan: meningitis, yellow fever, rabies, tifoid\n• Vaksin COVID-19 booster\n\nReservasi via aplikasi atau hubungi Poli Vaksinasi.",
                'keywords' => ['vaksin', 'vaksinasi', 'imunisasi', 'booster'],
                'priority' => 75,
            ],
            [
                'category' => 'Layanan',
                'question' => 'Apakah bisa konsultasi online (telemedicine)?',
                'answer' => 'Ya. Layanan telemedicine tersedia melalui aplikasi mobile RS Sehat Sentosa untuk konsultasi non-darurat, kontrol pasien lama, dan permintaan resep ulang. Dokter akan menghubungi via video call sesuai jadwal yang dipilih. Tarif konsultasi online sama dengan tatap muka.',
                'keywords' => ['telemedicine', 'konsultasi online', 'online consultation', 'video call dokter'],
                'priority' => 70,
            ],

            // ============================================================
            // HASIL PEMERIKSAAN
            // ============================================================
            [
                'category' => 'Hasil',
                'question' => 'Kapan hasil lab keluar?',
                'answer' => "Estimasi waktu hasil pemeriksaan:\n• Lab rutin (darah, urin): 2–6 jam\n• Mikrobiologi / kultur: 3–7 hari\n• X-Ray: 1–3 jam\n• USG: hasil interpretasi langsung setelah pemeriksaan\n• CT Scan / MRI: 1–2 hari kerja\n• PCR / swab: 1 hari\n\nUntuk pemeriksaan urgent, hasil dapat diprioritaskan dengan permintaan dari dokter.",
                'keywords' => ['kapan hasil', 'hasil lab keluar', 'waktu hasil', 'lama hasil'],
                'priority' => 75,
            ],
            [
                'category' => 'Hasil',
                'question' => 'Bagaimana cara mengambil hasil pemeriksaan lab/radiologi?',
                'answer' => "Pengambilan hasil:\n1. Datang ke loket Laboratorium / Radiologi (lantai 2) dengan struk pembayaran + KTP pasien\n2. Diwakilkan: surat kuasa bermaterai + KTP pasien + KTP pengambil\n3. Hasil digital dapat diunduh di aplikasi mobile setelah dikonfirmasi dokter\n\nUntuk interpretasi hasil, konsultasikan dengan dokter pemeriksa atau dokter rujukan Anda.",
                'keywords' => ['ambil hasil', 'pengambilan hasil', 'hasil lab', 'hasil radiologi', 'rontgen', 'usg', 'ct scan', 'mri'],
                'priority' => 80,
            ],
            [
                'category' => 'Hasil',
                'question' => 'Apakah hasil pemeriksaan bisa dikirim via WhatsApp atau email?',
                'answer' => "Ya, hasil pemeriksaan dapat dikirim secara digital:\n• Aplikasi mobile RS Sehat Sentosa (otomatis setelah dirilis dokter)\n• Email terdaftar (atas permintaan)\n• WhatsApp ke nomor terdaftar (atas permintaan, dengan persetujuan tertulis)\n\nPengiriman via email/WA memerlukan formulir persetujuan privasi yang ditandatangani.",
                'keywords' => ['kirim hasil', 'whatsapp hasil', 'email hasil', 'hasil online', 'hasil digital'],
                'priority' => 65,
            ],
            [
                'category' => 'Hasil',
                'question' => 'Apakah hasil pemeriksaan bisa diwakilkan?',
                'answer' => 'Bisa. Pengambilan hasil oleh perwakilan memerlukan: surat kuasa bermaterai Rp 10.000, fotokopi KTP pasien, dan KTP asli pengambil. Untuk hasil yang sangat sensitif (HIV, kehamilan, dll.) hanya diserahkan ke pasien langsung atau wali resmi.',
                'keywords' => ['wakilkan', 'diambilkan', 'surat kuasa', 'perwakilan'],
                'priority' => 65,
            ],

            // ============================================================
            // RAWAT INAP
            // ============================================================
            [
                'category' => 'Rawat Inap',
                'question' => 'Bagaimana prosedur rawat inap?',
                'answer' => "Setelah dokter menyatakan perlu rawat inap:\n1. Konfirmasi kelas kamar di bagian Admisi\n2. Tanda tangan persetujuan rawat inap (informed consent)\n3. Deposit (untuk pasien umum) atau verifikasi penjamin (BPJS/asuransi)\n4. Pasien diantar ke kamar oleh petugas\n\nDokumen yang perlu dibawa: KTP, kartu BPJS/asuransi, rujukan (jika ada).",
                'keywords' => ['rawat inap', 'opname', 'admisi', 'masuk rumah sakit', 'mondok'],
                'priority' => 85,
            ],
            [
                'category' => 'Rawat Inap',
                'question' => 'Kelas kamar apa saja yang tersedia?',
                'answer' => "Kelas kamar rawat inap:\n• Kelas 3 (6 bed) — Rp 350.000/malam\n• Kelas 2 (4 bed) — Rp 600.000/malam\n• Kelas 1 (2 bed) — Rp 1.000.000/malam\n• VIP (1 bed + sofa) — Rp 2.000.000/malam\n• VVIP / Suite — Rp 3.500.000/malam\n• ICU/HCU — Rp 2.500.000/malam\n\nUntuk pasien BPJS, kelas mengikuti hak kelas peserta. Naik kelas dikenakan selisih biaya.",
                'keywords' => ['kelas kamar', 'tarif kamar', 'biaya kamar', 'vip', 'icu', 'kamar'],
                'priority' => 80,
            ],
            [
                'category' => 'Rawat Inap',
                'question' => 'Apakah tersedia kamar rawat inap?',
                'answer' => 'Ketersediaan kamar berubah real-time. Untuk cek kamar kosong saat ini, hubungi bagian Admisi (021) 555-1234 ext. 200 atau cek di aplikasi mobile pada menu "Ketersediaan Kamar". Jika kelas yang diinginkan penuh, petugas akan menawarkan upgrade/downgrade.',
                'keywords' => ['kamar tersedia', 'kamar kosong', 'ketersediaan kamar', 'penuh', 'kosong'],
                'priority' => 75,
            ],
            [
                'category' => 'Rawat Inap',
                'question' => 'Apa saja syarat rawat inap?',
                'answer' => "Syarat rawat inap:\n• Surat pengantar rawat inap dari dokter (bisa dokter RS sendiri atau rujukan dari luar)\n• KTP pasien & penanggung jawab\n• Kartu BPJS aktif + rujukan (untuk BPJS) atau kartu asuransi (untuk asuransi)\n• Deposit awal (untuk pasien umum) — bervariasi sesuai kelas kamar dan diagnosa\n• Persetujuan tindakan medis bermaterai",
                'keywords' => ['syarat rawat inap', 'persyaratan opname', 'dokumen rawat inap'],
                'priority' => 75,
            ],

            // ============================================================
            // FASILITAS
            // ============================================================
            [
                'category' => 'Fasilitas',
                'question' => 'Apakah ada fasilitas parkir?',
                'answer' => 'Ya. Tersedia area parkir luas di basement (B1 & B2) dan parkir luar untuk motor. Tarif: motor Rp 3.000 (3 jam pertama), mobil Rp 5.000 (3 jam pertama), kemudian Rp 2.000/jam berikutnya. Tersedia juga parkir VIP dan parkir khusus disabilitas.',
                'keywords' => ['parkir', 'parking', 'parkir motor', 'parkir mobil', 'tarif parkir'],
                'priority' => 65,
            ],
            [
                'category' => 'Fasilitas',
                'question' => 'Apakah tersedia layanan ambulans?',
                'answer' => "Ya, ambulans 24 jam tersedia untuk:\n• Antar-jemput pasien IGD\n• Rujukan antar-RS\n• Stand-by event\n\nTarif tergantung jarak. Hubungi (021) 555-9119 (IGD) untuk panggilan ambulans. Ambulans dilengkapi paramedis dan peralatan dasar life support.",
                'keywords' => ['ambulans', 'ambulance', 'panggil ambulans'],
                'priority' => 75,
            ],
            [
                'category' => 'Fasilitas',
                'question' => 'Apakah ada apotek 24 jam?',
                'answer' => 'Ya. Apotek RS Sehat Sentosa buka 24 jam, melayani resep dokter dan obat bebas. Lokasi: lantai 1 dekat lobi utama dan lantai dasar dekat IGD. Pasien BPJS rawat jalan dengan resep dari poli BPJS dilayani gratis sesuai formularium.',
                'keywords' => ['apotek', 'farmasi', 'pharmacy', 'apotik'],
                'priority' => 75,
            ],
            [
                'category' => 'Fasilitas',
                'question' => 'Apakah ada kantin atau ATM?',
                'answer' => 'Ya. Kantin di lantai 1 buka 06.00–22.00 (menu makanan ringan & berat). ATM tersedia di lobi lantai 1 (BCA, Mandiri, BNI, BRI). Tersedia juga minimarket 24 jam dan mushola di setiap lantai.',
                'keywords' => ['kantin', 'atm', 'makan', 'minimarket', 'mushola', 'masjid'],
                'priority' => 50,
            ],

            // ============================================================
            // ADMINISTRASI / DOKUMEN
            // ============================================================
            [
                'category' => 'Dokumen',
                'question' => 'Bagaimana cara meminta surat keterangan sakit?',
                'answer' => 'Surat sakit otomatis diberikan oleh dokter setelah konsultasi jika kondisi pasien memerlukan istirahat. Jika perlu surat tambahan/duplikat, hubungi bagian Rekam Medis (lantai 1) dengan membawa KTP. Biaya legalisir Rp 25.000 per lembar.',
                'keywords' => ['surat sakit', 'surat keterangan sakit', 'sertifikat sakit'],
                'priority' => 70,
            ],
            [
                'category' => 'Dokumen',
                'question' => 'Bagaimana cara meminta resume medis?',
                'answer' => "Permintaan resume medis:\n1. Datang ke bagian Rekam Medis (lantai 1) dengan KTP pasien\n2. Isi formulir permintaan resume medis\n3. Diwakilkan: surat kuasa bermaterai + KTP\n4. Biaya: Rp 50.000\n5. Selesai dalam 3–7 hari kerja (bisa diambil langsung atau dikirim email)",
                'keywords' => ['resume medis', 'medical resume', 'discharge summary', 'ringkasan medis'],
                'priority' => 70,
            ],
            [
                'category' => 'Dokumen',
                'question' => 'Bagaimana cara meminta surat keterangan sehat?',
                'answer' => "Surat keterangan sehat (untuk SIM, sekolah, kerja, dll.):\n1. Daftar di loket pemeriksaan kesehatan / MCU Basic\n2. Konsultasi dokter umum + pemeriksaan fisik dasar\n3. Tambahan tes (jika diperlukan): buta warna, audiometri, rontgen\n4. Biaya mulai Rp 150.000 (tanpa tes tambahan)\n5. Surat dapat diambil langsung setelah pemeriksaan",
                'keywords' => ['surat sehat', 'surat keterangan sehat', 'sehat jasmani', 'surat dokter'],
                'priority' => 65,
            ],
            [
                'category' => 'Dokumen',
                'question' => 'Apakah bisa legalisir dokumen medis?',
                'answer' => 'Ya. Legalisir dokumen medis (resume, hasil pemeriksaan, surat dokter) dilakukan di bagian Rekam Medis (lantai 1). Bawa dokumen asli + fotokopi yang ingin dilegalisir + KTP. Biaya Rp 25.000 per lembar. Selesai dalam 1–3 hari kerja.',
                'keywords' => ['legalisir', 'legalisasi', 'cap basah', 'stempel'],
                'priority' => 55,
            ],
            [
                'category' => 'Dokumen',
                'question' => 'Bagaimana prosedur pengambilan rekam medis?',
                'answer' => "Rekam medis adalah milik RS, namun pasien berhak mendapatkan ringkasan / resume medis dan salinan hasil pemeriksaan. Prosedur:\n1. Permohonan tertulis di bagian Rekam Medis (lantai 1)\n2. Bawa KTP pasien (atau surat kuasa + KTP wali)\n3. Biaya administrasi sesuai jumlah lembar\n4. Selesai dalam 3–7 hari kerja\n\nUntuk keperluan hukum/asuransi, lampirkan surat keterangan tujuan.",
                'keywords' => ['rekam medis', 'medical record', 'riwayat berobat', 'data medis'],
                'priority' => 60,
            ],

            // ============================================================
            // KOMPLAIN & BANTUAN
            // ============================================================
            [
                'category' => 'Komplain',
                'question' => 'Bagaimana jika saya belum mendapat antrean?',
                'answer' => 'Jika nomor antrean belum muncul setelah pendaftaran online, mohon tunggu 5–10 menit dan refresh aplikasi. Jika tetap belum muncul, hubungi CS (021) 555-1234 atau datang ke loket informasi lantai 1. Petugas akan membantu mengecek status pendaftaran Anda.',
                'keywords' => ['belum antrean', 'antrean hilang', 'tidak dapat antrean'],
                'priority' => 65,
            ],
            [
                'category' => 'Komplain',
                'question' => 'Bisakah saya mengubah jadwal dokter?',
                'answer' => 'Bisa. Reschedule jadwal:\n• Via aplikasi mobile (paling cepat) — minimal 2 jam sebelum jadwal\n• Hubungi CS (021) 555-1234\n• Datang ke loket pendaftaran\n\nReschedule maksimal 3 kali per pendaftaran. Jika lebih dari itu, perlu daftar ulang.',
                'keywords' => ['ubah jadwal', 'reschedule', 'ganti jadwal', 'pindah jadwal'],
                'priority' => 65,
            ],
            [
                'category' => 'Komplain',
                'question' => 'Bagaimana cara membatalkan pendaftaran?',
                'answer' => 'Pembatalan pendaftaran:\n• Aplikasi mobile (sebelum hari H)\n• Hubungi CS (021) 555-1234 paling lambat 1 jam sebelum jadwal\n\nUntuk pendaftaran berbayar, dana akan dikembalikan dalam 3–7 hari kerja ke metode pembayaran asal (ada biaya administrasi 5%). Jika tidak datang tanpa pemberitahuan (no-show), dana tidak dikembalikan.',
                'keywords' => ['batalkan', 'cancel', 'pembatalan', 'refund'],
                'priority' => 65,
            ],
            [
                'category' => 'Komplain',
                'question' => 'Bagaimana jika saya mengalami kendala pembayaran?',
                'answer' => "Untuk kendala pembayaran (transfer gagal, double charge, virtual account error, dll.):\n1. Simpan bukti transaksi (screenshot/struk)\n2. Hubungi CS (021) 555-1234 atau bagian Keuangan\n3. Email ke keuangan@rsss.example dengan lampiran bukti\n\nSaya akan bantu meneruskan kasus Anda ke tim Keuangan untuk penyelesaian. Apakah Anda ingin disambungkan ke petugas?",
                'keywords' => ['kendala pembayaran', 'masalah bayar', 'transfer gagal', 'double charge'],
                'priority' => 65,
            ],
            [
                'category' => 'Komplain',
                'question' => 'Bagaimana cara menyampaikan keluhan atau komplain?',
                'answer' => "Saluran resmi pengaduan:\n• Email: pengaduan@rsss.example\n• Hotline keluhan: (021) 555-1234 ext. 999\n• Kotak saran di lobi lantai 1\n• Customer Service desk lantai 1\n\nSemua pengaduan akan ditindaklanjuti dalam 1×24 jam. Untuk kasus pelayanan medis serius, akan diteruskan ke Komite Medik. Saya akan bantu meneruskan keluhan Anda ke petugas customer service.",
                'keywords' => ['keluhan', 'komplain', 'komplen', 'pengaduan', 'lapor', 'kecewa'],
                'priority' => 70,
            ],
        ];
    }
}
