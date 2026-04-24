<?php

declare(strict_types=1);

use App\Libraries\Enums\CustomerContactEnum;
use App\Libraries\Enums\DayPeriodsEnum;
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
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('name', \intval(
                config('database.schema.sizes.client.name')
            ))->nullable(FALSE);
            $table->string('email')->nullable()->unique();
            $table->string('hostess', \intval(
                config('database.schema.sizes.client.hostess')
            ))->nullable();
            $table->timestamp('birthdate')->nullable();
            $table->enum(
                'contact',
                CustomerContactEnum::combineEnumValues()
            )->nullable();
            $table->enum(
                'schedule',
                DayPeriodsEnum::combineEnumValues()
            )->nullable();
            $table->timestamps();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
        });
        Schema::dropIfExists('customers');
    }
};
