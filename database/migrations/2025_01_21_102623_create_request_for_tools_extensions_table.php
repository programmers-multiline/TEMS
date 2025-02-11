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
        Schema::create('request_for_tools_extensions', function (Blueprint $table) {
            $table->id();
            $table->integer('tool_id');
            $table->integer('pe');
            $table->string('reason');
            $table->date('orig_end_date')->nullable();
            $table->date('extension_date');
            $table->integer('approver');
            $table->integer('approver_status')->default(0);
            $table->integer('status')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('request_for_tools_extensions');
    }
};
