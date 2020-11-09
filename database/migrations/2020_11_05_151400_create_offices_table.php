<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOfficesTable extends Migration
{
    /**
     * Adds the offices table from the database.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('offices', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('address');
        });
    }

    /**
     * Removes the offices table from the database.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('offices');
    }
}
