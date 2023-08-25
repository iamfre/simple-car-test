<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAdditionalToCarsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        if (!Schema::hasColumn('cars', 'additional')) {
            Schema::table('cars', function (Blueprint $table) {
                $table->longText('additional')->after('year')->nullable();
            });
        }

        if (!Schema::hasColumn('cars', 'external_id')) {
            Schema::table('cars', function (Blueprint $table) {
                $table->string('external_id')->after('id')->unique();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        if (Schema::hasColumn('cars', 'additional')) {
            Schema::table('cars', function (Blueprint $table) {
                $table->dropColumn('additional');
            });
        }

        if (Schema::hasColumn('cars', 'external_id')) {
            Schema::table('cars', function (Blueprint $table) {
                $table->dropColumn('external_id');
            });
        }
    }
}
