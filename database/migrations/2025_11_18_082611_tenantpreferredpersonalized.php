<?php
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
        Schema::table('tenants', function (Blueprint $table) {
               $table->integer('preferred_room_price')->nullable();
            $table->boolean('isPersonalized')->default(false);
            $table->string('preferred_location', 255)->nullable();
            $table->json('preferred_amenities')->nullable();
            $table->json('preferred_rules')->nullable();
            $table->json('preferred_features')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->dropColumn([
                'isPersonalized',
                'preferred_room_price',
                'preferred_location',
                'preferred_amenities',
                'preferred_rules',
                'preferred_features'
            ]);
        });
    }
};
