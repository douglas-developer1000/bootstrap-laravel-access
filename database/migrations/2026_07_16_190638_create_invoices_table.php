<?php

declare(strict_types=1);

use App\Libraries\Enums\GatewayTypeEnum;
use App\Libraries\Enums\InvoicePaymentTypeEnum;
use App\Libraries\Enums\InvoiceStatusEnum;
use App\Models\Invoice;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * @see Invoice::class
 */
return new class() extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('license_id')->constrained('licenses');

            $table->numericMorphs('licensable');
            $table->decimal('amount', 8, 2);

            $table->enum(
                'gateway',
                array_column(GatewayTypeEnum::cases(), 'value')
            )->nullable();

            $table->string('gateway_transaction_id')->nullable()->index();

            $table->enum(
                'status',
                array_column(InvoiceStatusEnum::cases(), 'value')
            )->default(
                InvoiceStatusEnum::PENDING->value
            );

            $table->enum(
                'payment_method',
                array_column(InvoicePaymentTypeEnum::cases(), 'value')
            )->nullable();

            $table->jsonb('payment_details')->nullable();

            $table->timestamp('voided_at')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropConstrainedForeignId('license_id');
        });
        Schema::dropIfExists('invoices');
    }
};
