<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SyncSupportTickets extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'support:sync-n8n';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync unsent support tickets to n8n';

    protected $supportService;

    public function __construct(\App\Services\SupportService $supportService)
    {
        parent::__construct();
        $this->supportService = $supportService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting sync of support tickets to n8n...');

        $tickets = \App\Models\SupportTicket::where('synced_to_n8n', false)->get();

        if ($tickets->isEmpty()) {
            $this->info('No unsynced tickets found.');
            return;
        }

        $this->info('Found ' . $tickets->count() . ' unsynced tickets.');

        $successCount = 0;
        $failCount = 0;

        foreach ($tickets as $ticket) {
            $this->line('Syncing ticket #' . $ticket->id . '...');
            
            if ($this->supportService->sendToN8n($ticket)) {
                $ticket->update(['synced_to_n8n' => true]);
                $this->info('Ticket #' . $ticket->id . ' synced successfully.');
                $successCount++;
            } else {
                $this->error('Failed to sync ticket #' . $ticket->id);
                $failCount++;
            }
        }

        $this->info("Sync completed. Success: $successCount, Failed: $failCount");
    }
}
