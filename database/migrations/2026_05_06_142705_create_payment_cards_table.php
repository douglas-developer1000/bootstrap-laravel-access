<?php

declare(strict_types=1);

use App\Libraries\Enums\CardPayWayEnum;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * @see App\Models\PaymentCard::class
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('payment_cards', function (Blueprint $table) {
            $table->id();
            $table->string(
                'flag',
                \intval(
                    config('database.schema.sizes.payment-card.flag.max')
                )
            )->nullable(FALSE);
            $table->enum(
                'pay_way',
                CardPayWayEnum::combineEnumValues()
            )->nullable(false);
            $table->string(
                'img',
                \intval(
                    config('database.schema.sizes.generic.img')
                )
            )->nullable(false);
            $table->boolean('native')->nullable(FALSE)->default(0);
            $table->softDeletes();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payment_cards', function (Blueprint $table) {
            $table->dropConstrainedForeignId('user_id');
        });
        Schema::dropIfExists('payment_cards');
    }
};
