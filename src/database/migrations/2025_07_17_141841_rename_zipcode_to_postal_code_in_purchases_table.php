<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenameZipcodeToPostalCodeInPurchasesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        if (Schema::hasColumn('purchases', 'zipcode')) {
            Schema::table('purchases', function (Blueprint $table) {
                $table->renameColumn('zipcode', 'postal_code');
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
        Schema::table('purchases', function (Blueprint $table) {
            $table->renameColumn('postal_code', 'zipcode');
        });
    }
}
