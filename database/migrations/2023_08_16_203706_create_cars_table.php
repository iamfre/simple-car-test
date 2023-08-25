<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCarsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('cars', function (Blueprint $table) {
            $table->id();
            $table->string('model')->index('by_model');
            $table->foreignId('brand_id')->constrained('brands');
            $table->unsignedDouble('price')->nullable();
            $table->unsignedDouble('sail_price')->nullable();
            $table->dateTime('year')->index('by_year')->nullable();
            $table->timestamps();

            $table->index(['brand_id', 'model'], 'by_brand_id_model');
            $table->unique(['model', 'brand_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('cars');
    }
}
