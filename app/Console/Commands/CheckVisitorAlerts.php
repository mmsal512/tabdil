<?php

namespace App\Console\Commands;

use App\Services\VisitorReportService;
use Illuminate\Console\Command;

class CheckVisitorAlerts extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'visitors:check-alerts';

    /**
     * The console command description.
     */
    protected $description = 'Check for traffic spikes or drops and send smart alerts';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Checking for traffic anomalies...');

        $reportService = new VisitorReportService();
        $alertSent = $reportService->checkAndSendSmartAlert();

        if ($alertSent) {
            $this->info('✓ Smart alert sent!');
        } else {
            $this->info('No anomalies detected.');
        }

        return Command::SUCCESS;
    }
}
