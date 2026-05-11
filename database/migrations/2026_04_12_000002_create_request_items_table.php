<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('request_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('request_id')->constrained('requests')->cascadeOnDelete();
            $table->string('jenis_aset');
            $table->unsignedInteger('kuantiti');
            $table->decimal('harga_seunit', 12, 2);
            $table->decimal('jumlah', 12, 2);
            $table->text('justifikasi')->nullable();
            $table->string('quotation')->nullable();
            $table->string('status')->default('Pending');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('request_items');
    }
};
