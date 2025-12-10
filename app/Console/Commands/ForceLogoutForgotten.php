<?php

namespace App\Console\Commands;

use App\Models\CardTransaction;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ForceLogoutForgotten  extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'attendance:force-logout';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Force logout all students who forgot to logout from the club';



    public function handle()
    {
        // Get the latest transaction ID for each card
        $lastTransactionIds = CardTransaction::select(DB::raw('MAX(id) as max_id'))
            ->groupBy('card_id')
            ->pluck('max_id');

        // Find all cards that have 'enter' as their last transaction (forgotten to logout)
        $forgotten = CardTransaction::whereIn('id', $lastTransactionIds)
            ->where('type', 'enter')
            ->get();

        foreach ($forgotten as $forget) {
            CardTransaction::create([
                'card_id' => $forget->card_id,
                'type' => 'Exit',
            ]);
        }

        $this->info('Force logout completed. ' . $forgotten->count() . ' students were logged out.');
    }
}



