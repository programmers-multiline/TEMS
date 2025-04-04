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
        Schema::create('upload_tools', function (Blueprint $table) {
            $table->id();
            $table->integer('upload_id');
            $table->integer('uploader_id');
            $table->integer('project_id');
            $table->integer('company_id');
            $table->integer('progress')->default(0);
            $table->integer('status')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('upload_tools');
    }
};
