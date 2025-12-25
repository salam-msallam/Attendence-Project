<?php

namespace App\Repositories;

use App\Models\CardTransaction;
use Illuminate\Support\Facades\DB;

class CardTransactionRepositories
{
    public function getEntryTransactionsByCardId($card_id)
    {
        return CardTransaction::where('card_id', $card_id)->where('type', 'enter')->orderBy('created_at', 'desc')->get();
    }

    public function getTransactionsByCardId($card_id, $startOfMonth, $endOfMonth)
    {
        return CardTransaction::where('card_id', $card_id)
            ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
            ->orderBy('created_at', 'asc')
            ->get();
    }

    public function LastLogin($card_id)
    {
        return CardTransaction::where('card_id', $card_id)->where('type', 'enter')->latest()->first();
    }

    public function LastLogout($card_id)
    {
        return  CardTransaction::where('card_id', $card_id)->where('type', 'Exit')->latest()->first();
    }

    public function findCardTransactionByCardID($card)
    {

        $cardTransaction = CardTransaction::where('card_id', $card->id)->latest()->first();
        return $cardTransaction;
    }
    public function createCardTransaction($card, $type)
    {
        $cardTransaction = CardTransaction::create([
            'card_id' => $card->id,
            'type' => $type
        ]);
        return $cardTransaction;
    }
    public function getAllTransactionsPaginated(int $perPage)
    {
        return DB::table('card-transaction')
            ->join('cards', 'card-transaction.card_id', '=', 'cards.id')
            ->join('users', 'cards.user_id', '=', 'users.id')
            ->select(
                'card-transaction.id',
                'card-transaction.created_at',
                'card-transaction.type',
                'cards.code as card_code',
                'users.id as user_id',
                'users.first_name',
                'users.last_name'
            )
            ->orderBy('card-transaction.created_at', 'desc')
            ->paginate($perPage); 
    }
}
