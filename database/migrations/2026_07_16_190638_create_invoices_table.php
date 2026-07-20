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
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('license_id')->constrained('licenses');

            $table->string('licensable_type', 245);
            $table->unsignedBigInteger('licensable_id');
            $table->index(['licensable_type', 'licensable_id'], null);

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
