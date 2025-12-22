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
        Schema::create('recipes', function (Blueprint $table) {
            $table->id();
            $table->string('dish_name', 255);
            $table->string('dish_image')->nullable();
            $table->integer('people_count');
            $table->enum('budget', ['thấp', 'trung bình', 'cao']);
            $table->string('dish_type')->nullable();
            $table->text('special_request')->nullable();
            $table->text('ingredients_json'); // Lưu danh sách nguyên liệu dạng JSON
            $table->text('cooking_instructions'); // Công thức nấu ăn
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
        Schema::dropIfExists('recipes');
    }
};
