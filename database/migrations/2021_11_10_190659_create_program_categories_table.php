<?php

use App\Constants\Status;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateProgramCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('program_categories', function(Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('specialist_id')->nullable();
            $table->enum('status', ['pending', 'active', 'for_checking'])->default('pending');
            $table->string('name');
            $table->timestamps();

            $table->foreign('specialist_id')->references('id')->on('specialists')->nullOnDelete();
        });

        DB::table('program_categories')->insert([
            ['status' => Status::ACTIVE, 'name' => 'Сессия'],
            ['status' => Status::ACTIVE, 'name' => 'Мастер класс'],
            ['status' => Status::ACTIVE, 'name' => 'Тренинг'],
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('program_categories', function(Blueprint $table) {
            $table->dropForeign(['specialist_id']);
        });

        Schema::dropIfExists('program_categories');
    }
}
