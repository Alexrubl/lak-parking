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
        Schema::create('histories', function (Blueprint $table) {
            $table->id();
            $table->integer('controller_id')->nullabled();
            $table->integer('tenant_id')->nullabled();
            $table->integer('transport_id')->nullabled();
            $table->integer('price')->nullabled();
            $table->string('comment')->nullabled();
            $table->string('image')->nullabled();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('histories');
    }
};
