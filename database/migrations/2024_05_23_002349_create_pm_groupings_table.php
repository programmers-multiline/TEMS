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
        Schema::create('pm_groupings', function (Blueprint $table) {
            $table->id();
            $table->integer('pm_code')->nullable();
            $table->string('project_manager');
            $table->integer('pe_code')->nullable();
            $table->string('project_engineer');
            $table->integer('status')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pm_groupings');
    }
};
