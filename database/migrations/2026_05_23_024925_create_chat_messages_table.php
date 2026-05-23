<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('chat_messages', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('conversation_id')
                ->constrained('chat_conversations')
                ->cascadeOnDelete();
            $table->string('role'); // user | assistant | system
            $table->text('content');
            $table->string('intent')->nullable()->index(); // emergency, medical_advice, faq, schedule, general, blocked
            $table->boolean('was_blocked')->default(false)->index();
            $table->boolean('used_llm')->default(false);
            $table->string('model')->nullable();
            $table->jsonb('metadata')->nullable(); // tokens, retrieved_ids, dst
            $table->timestamps();

            $table->index(['conversation_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chat_messages');
    }
};
