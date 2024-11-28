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
        Schema::create('pe_logs', function (Blueprint $table) {
            $table->id();
            $table->integer('request_number');
            $table->integer('tool_id');
            $table->integer('upload_id');
            $table->integer('pe');
            $table->string('remarks');
            $table->integer('status')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pe_logs');
    }
};
