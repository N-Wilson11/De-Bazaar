<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Bid;
use Carbon\Carbon;

class ExpireBids extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bids:expire';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Set expired bids to "expired" status';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $expiredBids = Bid::where('status', Bid::STATUS_PENDING)
            ->where('expires_at', '<=', Carbon::now())
            ->get();

        $count = 0;
        foreach ($expiredBids as $bid) {
            $bid->status = Bid::STATUS_EXPIRED;
            $bid->save();
            $count++;
        }

        $this->info("Expired {$count} bids.");
        
        return 0;
    }
}
