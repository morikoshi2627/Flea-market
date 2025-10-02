<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('items', function (Blueprint $table) {
            $table->id();

            // 外部キー
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->unsignedBigInteger('buyer_id')->nullable(); // 追加された buyer_id
            $table->foreignId('brand_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('condition_id')->constrained()->onDelete('cascade');

            // 基本情報
            $table->string('name');
            $table->integer('price');
            $table->text('description');
            $table->string('image');
            $table->string('status')->default('selling'); // 追加された status

            $table->timestamps();
        });
    }
    
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('items');
    }
}
