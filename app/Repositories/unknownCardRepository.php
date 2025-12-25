<?php

namespace App\Repositories;
use App\Models\unknownCard;

class unknownCardRepository {
    public function getAllCardsCodes()
    {
        $AllCardsCodes = UnknownCard::pluck('code');
        return $AllCardsCodes;
    }
}
