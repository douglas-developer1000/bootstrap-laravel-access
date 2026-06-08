<?php

declare(strict_types=1);

use App\Libraries\Enums\StockExitTypeEnum;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * @see App\Models\StockExit::class
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('stock_exits', function (Blueprint $table) {
            $table->id();
            $table->enum(
                'type',
                array_column(StockExitTypeEnum::cases(), 'value')
            )->nullable(FALSE);
            $table->unsignedMediumInteger('qty')->nullable(FALSE);

            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('stock_entry_id')->constrained()->cascadeOnDelete();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stock_exits', function (Blueprint $table) {
            $table->dropConstrainedForeignId('user_id');
            $table->dropConstrainedForeignId('stock_entry_id');
        });
        Schema::dropIfExists('stock_exits');
    }
};
