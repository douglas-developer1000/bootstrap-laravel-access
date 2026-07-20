<?php

declare(strict_types=1);

use App\Models\Credit;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * @see Credit::class
 */
return new class() extends Migration
{
    /**
     * Run the migrations.
     *
     * NOTE 1: the licensable_type size must not be greater than 245, because of
     * each character has 4 bytes and the index in VARCHAR(255) would have 1020
     * bytes (and the max is 1000 bytes).
     *
     * NOTE 2: the licensable_type and licensable_id declaration below is
     * equivalent to "$table->numericMorphs('licensable')".
     */
    public function up(): void
    {
        Schema::create('credits', function (Blueprint $table) {
            $table->id();

            $table->string('licensable_type', 245);
            $table->unsignedBigInteger('licensable_id');
            $table->index(['licensable_type', 'licensable_id'], null);

            $table->decimal('amount', 8, 2);
            $table->string('description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('credits');
    }
};
