<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('libraries_report_files', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('reportable_type');
            $table->uuid('reportable_id');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamp('created_on')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamp('updated_on')->nullable();
            $table->unsignedInteger('version');
            $table->string('file_path');
            $table->timestamp('generated_at')->nullable();

            $table->unique(['reportable_type', 'reportable_id', 'version']);
            $table->index(['reportable_type', 'reportable_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('libraries_report_files');
    }
};
