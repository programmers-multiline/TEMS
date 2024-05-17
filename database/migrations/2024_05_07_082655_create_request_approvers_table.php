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
        Schema::create('request_approvers', function (Blueprint $table) {
            $table->id();
            $table->integer('request_id');
            $table->integer('approver_id')->nullable();
            $table->integer('approver_status');
            $table->integer('sequence');
            $table->integer('series')->nullable();
            $table->integer('status')->default('1');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('request_approvers');
    }
};
