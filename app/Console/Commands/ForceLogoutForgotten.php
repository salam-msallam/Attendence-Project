<?php

namespace App\Console\Commands;

use App\Models\CardTransaction;
use Illuminate\Console\Command;
use Illuminate\Container\Attributes\Auth;
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
    protected $description = 'Command description';

  

    public function handle()
    {
        $lastTransactionIds = CardTransaction::select(DB::raw('MAX(id)'))->groupBy('card_id')->pluck(DB::raw('MAX(id)'));
        $forgetten = CardTransaction::whereIn('id', $lastTransactionIds)->where('type','enter')->get();
        foreach ($forgetten as $forget) {
            CardTransaction::create([
                'card_id'=>$forget->card_id,
                'type'=>'Exit',
            ]);
        }
    }
}


    
