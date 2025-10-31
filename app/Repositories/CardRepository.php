<?php
 
 namespace App\Repository;
 use App\Models\Card;
 use App\Models\User;

 class CardRepository{

    function getAllCards(){
        $AllCards=Card::all();
        return $AllCards;

    }

    function findCardByID($id){

        $card = Card::find($id);
        return $card;
    }

    public function updateCard(int $id, array $data) 
    {
        $card = Card::find($id);

        if ($card) {
            $dataToUpdate = [
                'code' => $data['code'] ?? $card->code 
            ]; 
            $card->update($dataToUpdate); 
        }

        return $card; 
    }

    function createCardForUser(User $user, array $data){
            $card = $user->card()->create($data);
            return $card;
        
        
    }
    function findCardByCode($code){
        $card=Card::where('code',$code)->first();
        return $card;
    }
    // function findCardByUserID($user){
    //     $card=Card::where('user_id',$user->id)->first();
    //     return $card;
    // }
    public function FindCardByUserID($user_id){
        $card =Card::where('user_id',$user_id)->first();
        return $card;
    }

}
