<?php

namespace App\Services;
use App\Models\unknownCard;
use App\Repositories\unknownCardRepository;
use App\Exceptions\ModelNotFoundException;

class UnknownCardServices {

    protected $unknownCardRepository;
    public function __construct(unknownCardRepository $unknownCardRepository){
        $this->unknownCardRepository=$unknownCardRepository;
    }

    public function getUnknownCards()
    {
        $AllCardsCodes = $this->unknownCardRepository->getAllCardsCodes();
        if (!$AllCardsCodes) {
            throw new ModelNotFoundException();
        }
        return $AllCardsCodes;
    }
}
