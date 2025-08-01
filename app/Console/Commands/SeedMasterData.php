<?php

namespace App\Console\Commands;

use Database\Seeders\MasterDataSeeder;
use Illuminate\Console\Command;

class SeedMasterData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:seed-master 
                            {--fresh : データベースをリフレッシュしてからシードを実行}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '画面確認用のマスターデータを投入します';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        if ($this->option('fresh')) {
            $this->info('データベースをリフレッシュしています...');
            $this->call('migrate:fresh');
        }

        $this->info('マスターデータを投入しています...');
        $this->call('db:seed', ['--class' => MasterDataSeeder::class]);

        $this->newLine();
        $this->info('✅ マスターデータの投入が完了しました！');

        return Command::SUCCESS;
    }
}
