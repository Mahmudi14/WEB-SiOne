<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('answers', function (Blueprint $table) {
            $table->id();

            $table->foreignId('room_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('participant_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('question_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('question_option_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->boolean('is_correct');

            $table->timestamp('submitted_at');

            $table->timestamps();

            $table->unique([
                'participant_id',
                'question_id',
            ]);

            $table->index([
                'room_id',
                'question_id',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('answers');
    }
};
