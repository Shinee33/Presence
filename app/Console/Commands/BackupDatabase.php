<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class BackupDatabase extends Command
{
    protected $signature = 'db:backup';
    protected $description = 'Backup database to SQL file';

    public function handle()
    {
        $dbName = config('database.connections.mysql.database');
        $user = config('database.connections.mysql.username');
        $password = config('database.connections.mysql.password');
        $host = config('database.connections.mysql.host');

        $backupPath = storage_path("app/backup/{$dbName}_" . date('Y_m_d_His') . ".sql");

        // Pastikan direktori backup ada
        File::ensureDirectoryExists(storage_path("app/backup"));

        $mysqldumpPath = 'D:\b4b\Xampp\mysql\bin\mysqldump.exe'; // Ubah sesuai lokasi kamu
        $command = "\"{$mysqldumpPath}\" -h {$host} -u {$user} -p\"{$password}\" {$dbName} > \"{$backupPath}\"";

        $returnVar = NULL;
        $output = NULL;
        exec($command, $output, $returnVar);

        if ($returnVar !== 0) {
            $this->error('Gagal membackup database.');
            return 1;
        }

        $this->info("Database berhasil dibackup ke: {$backupPath}");
        return 0;
    }
}
