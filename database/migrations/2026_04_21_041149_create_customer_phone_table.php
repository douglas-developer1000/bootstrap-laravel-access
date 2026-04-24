<?php

declare(strict_types=1);

use App\Libraries\Enums\CustomerPhoneTypeEnum;
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
        Schema::create('customer_phone', function (Blueprint $table) {
            $table->id();
            $table->enum('type', CustomerPhoneTypeEnum::combineEnumValues());
            $table->string(
                'number',
                \intval(
                    config('database.schema.sizes.client.phone')
                )
            );

            $table->foreignId('customer_id')->constrained()->onDelete('cascade');

            $table->timestamp('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customer_phone', function (Blueprint $table) {
            $table->dropForeign(['customer_id']);
        });
        Schema::dropIfExists('customer_phone');
    }
};
