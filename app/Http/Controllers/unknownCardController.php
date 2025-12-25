<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Card;
use App\Models\UnknownCard;
use App\Models\CardTransaction;
use App\Services\UnknownCardServices;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class unknownCardController extends Controller
{

    protected $unknownCardServices;
    public function __construct(UnknownCardServices $unknownCardServices)
    {
        $this->unknownCardServices = $unknownCardServices;
    }
    public function processScan($code)
    {
        UnknownCard::Create([
            'code' => $code,
            'updated_at' => Carbon::now()
        ]);
        Log::warning("Unauthorized Scan: Card code [$code] is not in our records. Logged to unknown_cards table.");
    }

    public function getUnknownCards()
    {
        $unknownCards = $this->unknownCardServices->getUnknownCards();
        return response()->json([
            'code' => 200,
            'message' => 'Successfully retrieved unknown card scans.',
            'data' => [
                'code'   => $unknownCards
            ]
        ], 200);
    }
}
