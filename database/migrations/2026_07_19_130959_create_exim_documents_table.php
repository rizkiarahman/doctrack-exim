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
        Schema::create('exim_documents', function (Blueprint $table) {
            $table->id();
            $table->string('no_aju')->unique();
            $table->string('pic');
            $table->date('tgl_diserahkan');
            $table->date('tgl_kembali')->nullable();
            $table->string('status')->default('Menunggu Tanda Tangan');
            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exim_documents');
    }
};
