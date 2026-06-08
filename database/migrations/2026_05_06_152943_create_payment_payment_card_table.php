<?php

declare(strict_types=1);

use App\Libraries\Enums\CardPayWayEnum;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * @see App\Models\PaymentPaymentCard::class
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('payment_payment_card', function (Blueprint $table) {
            $table->id();
            $table->enum(
                'pay_way',
                array_column(CardPayWayEnum::cases(), 'value')
            );
            $table->foreignId('payment_id')->constrained()->cascadeOnDelete();
            $table->foreignId('payment_card_id')->constrained()->cascadeOnDelete();

            $table->unsignedBigInteger('fee_id')->nullable(TRUE);
            $table->foreign('fee_id')
                ->references('id')->on('discounts');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payment_payment_card', function (Blueprint $table) {
            $table->dropConstrainedForeignId('payment_id');
            $table->dropConstrainedForeignId('payment_card_id');

            $table->dropForeign(['fee_id']);
        });
        Schema::dropIfExists('payment_payment_card');
    }
};
