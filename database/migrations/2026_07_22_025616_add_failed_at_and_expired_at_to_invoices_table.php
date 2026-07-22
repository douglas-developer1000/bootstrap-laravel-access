<?php

declare(strict_types=1);

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
     * NOTE 1: The columns "failed_at" and "expired_at" are only used on the
     * context of gateway's payment fail. The "failed_at" column is updated
     * (updating the "status" column to "failed" too) always when occurs
     * the payment fail by gateway. The "expired_at" column is only
     * updated during the "clean job" diary execution, when is detected
     * that the invoice has status column with value "failed".
     *
     * NOTE 2: The "voided_at" column (declared previously) is used during
     * cancelling by user.
     */
    public function up(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->timestamp('failed_at')->nullable();
            $table->timestamp('expired_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn('failed_at', 'expired_at');
        });
    }
};
