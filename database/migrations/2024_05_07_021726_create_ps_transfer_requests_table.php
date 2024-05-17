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
        Schema::create('ps_transfer_requests', function (Blueprint $table) {
            $table->id();
            $table->integer('request_number');
            $table->integer('user_id');
            $table->string('subcon');
            $table->string('customer_name');
            $table->string('project_name');
            $table->string('project_code');
            $table->string('project_address');
            $table->date('date_requested');
            $table->string('request_status')->default('pending');
            $table->string('progress')->default('ongoing');
            $table->integer('status')->default('1');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ps_transfer_requests');
    }
};
