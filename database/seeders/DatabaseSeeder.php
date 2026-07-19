<?php

namespace Database\Seeders;

use App\Models\EximDocument;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Seed default Admin
        User::create([
            'name' => 'Admin EXIM',
            'email' => 'admin@exim.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        // Seed default User
        User::create([
            'name' => 'Staff EXIM',
            'email' => 'user@exim.com',
            'password' => Hash::make('password'),
            'role' => 'user',
        ]);

        // Document 1: Already returned, signed quickly (3 days duration)
        EximDocument::create([
            'no_aju' => 'AJU-2026-0001',
            'pic' => 'Budi Santoso',
            'tgl_diserahkan' => now()->subDays(15),
            'tgl_kembali' => now()->subDays(12),
            'status' => 'Sudah Kembali',
            'catatan' => 'Lancar, ditandatangani supervisor tanpa kendala'
        ]);

        // Document 2: Already returned, signed in 5 days
        EximDocument::create([
            'no_aju' => 'AJU-2026-0002',
            'pic' => 'Ani Wijaya',
            'tgl_diserahkan' => now()->subDays(12),
            'tgl_kembali' => now()->subDays(7),
            'status' => 'Sudah Kembali',
            'catatan' => 'Sempat tertunda revisi draft PIB'
        ]);

        // Document 3: Active pending document, within deadline (2 days ago)
        EximDocument::create([
            'no_aju' => 'AJU-2026-0003',
            'pic' => 'David Christian',
            'tgl_diserahkan' => now()->subDays(2),
            'tgl_kembali' => null,
            'status' => 'Menunggu Tanda Tangan',
            'catatan' => 'Dokumen PIB impor bahan baku manufaktur'
        ]);

        // Document 4: Overdue pending document (10 days ago) - will trigger Perlu Follow Up
        EximDocument::create([
            'no_aju' => 'AJU-2026-0004',
            'pic' => 'Rina Permata',
            'tgl_diserahkan' => now()->subDays(10),
            'tgl_kembali' => null,
            'status' => 'Menunggu Tanda Tangan',
            'catatan' => 'Dokumen Certificate of Origin (COO) ekspor kopi ke Jepang'
        ]);

        // Document 5: Overdue pending document (12 days ago) - will trigger Perlu Follow Up
        EximDocument::create([
            'no_aju' => 'AJU-2026-0005',
            'pic' => 'Eko Prasetyo',
            'tgl_diserahkan' => now()->subDays(12),
            'tgl_kembali' => null,
            'status' => 'Menunggu Tanda Tangan',
            'catatan' => 'Dokumen packing list & invoice ekspor besi baja'
        ]);
    }
}
