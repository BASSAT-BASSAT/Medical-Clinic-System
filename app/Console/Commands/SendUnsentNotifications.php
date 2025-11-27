<?php

namespace App\Console\Commands;

use App\Services\NotificationService;
use Illuminate\Console\Command;

class SendUnsentNotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notifications:send-unsent';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send all unsent notifications';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Sending unsent notifications...');

        $count = NotificationService::sendUnsentNotifications();

        $this->info("Successfully sent $count notifications.");

        return Command::SUCCESS;
    }
}
