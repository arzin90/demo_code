<?php

use App\Constants\Status;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateChaptersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('chapters', function(Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('specialist_id')->nullable();
            $table->enum('status', ['pending', 'active', 'for_checking'])->default('pending');
            $table->string('name');
            $table->timestamps();

            $table->foreign('specialist_id')->references('id')->on('specialists')->nullOnDelete();
        });

        DB::table('chapters')->insert([
            ['status' => Status::ACTIVE, 'name' => 'Менеджмент и стратегия'],
            ['status' => Status::ACTIVE, 'name' => 'Маркетинг и реклама'],
            ['status' => Status::ACTIVE, 'name' => 'Психологические программы и личностные тренинги'],
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('chapters', function(Blueprint $table) {
            $table->dropForeign(['specialist_id']);
        });

        Schema::dropIfExists('chapters');
    }
}
