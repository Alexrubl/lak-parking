<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('controllers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('ip');
            $table->string('apikey');
            $table->boolean('active')->default(0);
            $table->string('id_open_stream')->nullable();
            $table->string('id_close_stream')->nullable();
            $table->string('url_open')->nullable();
            $table->string('url_close')->nullable();
            $table->string('method')->nullable();
            $table->integer('pause')->nullable()->default(10);
            $table->text('cameras')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('controllers');
    }
};
