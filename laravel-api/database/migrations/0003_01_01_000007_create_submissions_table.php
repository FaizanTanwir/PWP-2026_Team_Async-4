<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('unit_id')->constrained()->onDelete('cascade');

            // These fields capture the "generated" question so you can review it later
            $table->string('type');             // 'mcq', 'scramble', 'translation'
            $table->text('question_text');      // e.g., "Translate: 'The cat is blue'"
            $table->text('provided_answer');    // What the student typed/selected
            $table->text('correct_answer');     // The actual correct value
            $table->float('accuracy', 3, 2);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('submissions');
    }
};
