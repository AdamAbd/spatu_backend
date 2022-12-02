<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();

            // //* Below code used for planet scale database
            // $table->bigInteger('brand_id')->nullable();

            //* Below code used for standar databases
            $table->foreignId('brand_id')->constrained()->onDelete('cascade');

            $table->string('title')->unique();
            $table->decimal('rating');
            $table->integer('reviews_total');
            $table->integer('solds_total');
            $table->text('description');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('products');
    }
};
