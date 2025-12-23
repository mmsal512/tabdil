<?php

namespace App\Console\Commands;

use App\Services\VisitorReportService;
use Illuminate\Console\Command;

class SendVisitorReport extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'visitors:send-report {--force : Force send even if interval not met}';

    /**
     * The console command description.
     */
    protected $description = 'Send visitor statistics report to Telegram';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Checking if visitor report should be sent...');

        $reportService = new VisitorReportService();
        
        if ($this->option('force')) {
            // Force send by using the smart alert channel
            $this->info('Force sending report...');
        }

        $success = $reportService->sendReport();

        if ($success) {
            $this->info('✓ Visitor report sent successfully!');
            return Command::SUCCESS;
        }

        $this->info('Report not sent (interval not met or no visitors).');
        return Command::SUCCESS;
    }
}
