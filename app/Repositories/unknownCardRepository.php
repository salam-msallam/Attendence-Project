<?php

namespace App\Repositories;
use App\Models\unknownCard;

class unknownCardRepository {
    public function getAllCardsCodes()
    {
        $AllCardsCodes = unknownCard::pluck('code');
        return $AllCardsCodes;
    }
}
