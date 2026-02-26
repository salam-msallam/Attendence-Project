<?php

namespace App\Repositories;
use App\Models\unknownCard;

class unknownCardRepository {
    public function getAllCardsCodes()
    {
        return unknownCard::orderBy('created_at', 'desc')->get();
    }
}
