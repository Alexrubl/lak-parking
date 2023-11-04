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
            $table->string('id_stream')->nullabled();
            $table->string('url_open')->nullabled();
            $table->string('Url_close')->nullabled();
            $table->string('method')->nullabled();
            $table->number('pause')->nullabled()->default(10);
            $table->text('cameras')->nullabled();
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
