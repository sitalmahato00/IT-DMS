<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;

class CreateSessionsTable extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:create-sessions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create sessions table if it does not exist';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if (Schema::hasTable('sessions')) {
            $this->info('✓ Sessions table already exists.');
            return Command::SUCCESS;
        }

        $this->info('Creating sessions table...');

        Schema::create('sessions', function ($table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });

        $this->info('✓ Sessions table created successfully!');
        return Command::SUCCESS;
    }
}

