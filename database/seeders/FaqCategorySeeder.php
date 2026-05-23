<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\FaqCategory;
use Illuminate\Database\Seeder;

class FaqCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Umum', 'description' => 'Lokasi, kontak, jam operasional, jam besuk.', 'icon' => 'info', 'color' => '#64748b', 'sort_order' => 10],
            ['name' => 'Pendaftaran', 'description' => 'Cara daftar, appointment, antrean.', 'icon' => 'clipboard-list', 'color' => '#2563eb', 'sort_order' => 20],
            ['name' => 'Dokter', 'description' => 'Spesialisasi, status praktik & cuti.', 'icon' => 'user-round', 'color' => '#0ea5e9', 'sort_order' => 30],
            ['name' => 'Biaya', 'description' => 'Tarif, metode pembayaran, cicilan.', 'icon' => 'wallet', 'color' => '#f59e0b', 'sort_order' => 40],
            ['name' => 'BPJS', 'description' => 'Alur, rujukan, poli BPJS.', 'icon' => 'shield-check', 'color' => '#16a34a', 'sort_order' => 50],
            ['name' => 'Asuransi', 'description' => 'Asuransi swasta, klaim cashless & reimburse.', 'icon' => 'shield', 'color' => '#0d9488', 'sort_order' => 60],
            ['name' => 'Layanan', 'description' => 'IGD, lab, radiologi, MCU, vaksin, telemedicine.', 'icon' => 'stethoscope', 'color' => '#dc2626', 'sort_order' => 70],
            ['name' => 'Hasil', 'description' => 'Waktu rilis, pengambilan, pengiriman digital.', 'icon' => 'file-text', 'color' => '#7c3aed', 'sort_order' => 80],
            ['name' => 'Rawat Inap', 'description' => 'Prosedur, kelas kamar, syarat opname.', 'icon' => 'bed', 'color' => '#db2777', 'sort_order' => 90],
            ['name' => 'Fasilitas', 'description' => 'Parkir, ambulans, apotek 24 jam, kantin, ATM.', 'icon' => 'building-2', 'color' => '#0891b2', 'sort_order' => 100],
            ['name' => 'Dokumen', 'description' => 'Surat sakit, resume medis, legalisir, rekam medis.', 'icon' => 'file-signature', 'color' => '#9333ea', 'sort_order' => 110],
            ['name' => 'Komplain', 'description' => 'Pengaduan, ubah jadwal, batalkan, kendala bayar.', 'icon' => 'message-square-warning', 'color' => '#ea580c', 'sort_order' => 120],
            ['name' => 'IGD', 'description' => 'Alur dan lokasi instalasi gawat darurat.', 'icon' => 'siren', 'color' => '#dc2626', 'sort_order' => 130],
        ];

        foreach ($categories as $row) {
            FaqCategory::query()->updateOrCreate(['name' => $row['name']], $row + ['is_active' => true]);
        }
    }
}
