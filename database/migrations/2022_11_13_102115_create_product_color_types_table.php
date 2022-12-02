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
        Schema::create('product_color_types', function (Blueprint $table) {
            $table->id();

            // //* Below code used for planet scale database
            // $table->bigInteger('product_id')->nullable();

            //* Below code used for standar databases
            $table->foreignId('product_id')->constrained()->onDelete('cascade');

            $table->text('image');

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
        Schema::dropIfExists('product_color_types');
    }
};
