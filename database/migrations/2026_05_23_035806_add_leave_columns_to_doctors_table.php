<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('doctors', function (Blueprint $table): void {
            $table->date('leave_start_date')->nullable()->after('photo_url');
            $table->date('leave_end_date')->nullable()->after('leave_start_date');
            $table->string('leave_reason')->nullable()->after('leave_end_date');
        });
    }

    public function down(): void
    {
        Schema::table('doctors', function (Blueprint $table): void {
            $table->dropColumn(['leave_start_date', 'leave_end_date', 'leave_reason']);
        });
    }
};
