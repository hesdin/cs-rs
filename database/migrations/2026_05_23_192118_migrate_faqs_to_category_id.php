<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Tambah kolom category_id (nullable dulu untuk backfill)
        Schema::table('faqs', function (Blueprint $table): void {
            $table->foreignId('category_id')
                ->nullable()
                ->after('id')
                ->constrained('faq_categories')
                ->restrictOnDelete();
        });

        // 2. Backfill: untuk setiap nilai distinct di faqs.category, buat row di
        //    faq_categories (jika belum ada) lalu update faqs.category_id.
        $existingNames = DB::table('faqs')
            ->select('category')
            ->whereNotNull('category')
            ->distinct()
            ->pluck('category');

        foreach ($existingNames as $index => $name) {
            $name = trim((string) $name);
            if ($name === '') {
                continue;
            }

            $slug = Str::slug($name);

            $id = DB::table('faq_categories')->where('name', $name)->value('id');
            if ($id === null) {
                $id = DB::table('faq_categories')->insertGetId([
                    'name' => $name,
                    'slug' => $slug,
                    'sort_order' => $index,
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            DB::table('faqs')->where('category', $name)->update(['category_id' => $id]);
        }

        // 3. Wajibkan category_id setelah backfill
        Schema::table('faqs', function (Blueprint $table): void {
            $table->foreignId('category_id')->nullable(false)->change();
        });

        // 4. Drop index lama (faqs_category_index) sebelum drop kolom
        Schema::table('faqs', function (Blueprint $table): void {
            $table->dropIndex('faqs_category_index');
        });

        Schema::table('faqs', function (Blueprint $table): void {
            $table->dropColumn('category');
        });
    }

    public function down(): void
    {
        Schema::table('faqs', function (Blueprint $table): void {
            $table->string('category')->nullable()->after('id');
        });

        // Restore string dari relasi
        $rows = DB::table('faqs')
            ->join('faq_categories', 'faqs.category_id', '=', 'faq_categories.id')
            ->select('faqs.id', 'faq_categories.name')
            ->get();

        foreach ($rows as $row) {
            DB::table('faqs')->where('id', $row->id)->update(['category' => $row->name]);
        }

        Schema::table('faqs', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('category_id');
            $table->string('category')->nullable(false)->change();
        });
    }
};
