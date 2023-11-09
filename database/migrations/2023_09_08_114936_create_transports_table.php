<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTransportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transports', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->integer('tenant_id')->nullabled();
            $table->integer('rate_id')->nullabled();
            $table->string('number');
            $table->integer('type_id');
            $table->boolean('guest')->nullable()->default(true);
            $table->string('driver')->nullable();
            $table->string('access')->nullable();
            $table->string('restrictions')->nullable();
            $table->text('week')->nullable();
            $table->integer('time_limit')->nullabled()->default(0);
            $table->string('fromTime')->nullable();
            $table->string('toTime')->nullable();
            $table->string('fromDate')->nullable();
            $table->string('toDate')->nullable();
            $table->boolean('inside')->default(0)->nullabled();
            $table->softDeletes();
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
        Schema::table('transports', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
        Schema::dropIfExists('transports');
    }
}
