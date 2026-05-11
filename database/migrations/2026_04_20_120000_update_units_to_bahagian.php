<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $mapping = [
            'Unit Pendaftaran' => 'Bahagian Pendaftaran',
            'Unit Hasil' => 'Bahagian Hasil',
            'Unit Pembangunan' => 'Bahagian Pembangunan',
            'Unit Khidmat Pengurusan' => 'Bahagian Khidmat Pengurusan',
            'Unit Teknikal' => 'Bahagian Teknikal',
            'Unit Pembangunan Tanah & Pelupusan' => 'Bahagian Pembangunan Tanah & Pelupusan',
        ];

        foreach ($mapping as $oldValue => $newValue) {
            DB::table('users')->where('unit', $oldValue)->update(['unit' => $newValue]);
            DB::table('requests')->where('unit', $oldValue)->update(['unit' => $newValue]);
        }
    }

    public function down(): void
    {
        $mapping = [
            'Bahagian Pendaftaran' => 'Unit Pendaftaran',
            'Bahagian Hasil' => 'Unit Hasil',
            'Bahagian Pembangunan' => 'Unit Pembangunan',
            'Bahagian Khidmat Pengurusan' => 'Unit Khidmat Pengurusan',
            'Bahagian Teknikal' => 'Unit Teknikal',
            'Bahagian Pembangunan Tanah & Pelupusan' => 'Unit Pembangunan Tanah & Pelupusan',
        ];

        foreach ($mapping as $oldValue => $newValue) {
            DB::table('users')->where('unit', $oldValue)->update(['unit' => $newValue]);
            DB::table('requests')->where('unit', $oldValue)->update(['unit' => $newValue]);
        }
    }
};
