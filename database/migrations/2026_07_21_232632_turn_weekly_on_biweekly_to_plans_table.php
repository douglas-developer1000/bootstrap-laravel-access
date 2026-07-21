<?php

declare(strict_types=1);

use App\Libraries\Enums\BillingPeriodEnum;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * @see App\Models\Plan::class
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('plans', function (Blueprint $table) {
            $table->enum(
                'billing_period',
                array_column(BillingPeriodEnum::cases(), 'value')
            )->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('plans', function (Blueprint $table) {
            $values = [
                BillingPeriodEnum::NONE->value,
                'weekly',
                BillingPeriodEnum::MONTHLY->value,
                BillingPeriodEnum::QUARTERLY->value,
                BillingPeriodEnum::YEARLY->value,
            ];
            $table->enum('billing_period', $values)->change();
        });
    }
};
