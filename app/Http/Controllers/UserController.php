<?php

namespace App\Http\Controllers;


use App\Http\Requests\UpdateUserValidateRequest;
use App\Http\Requests\UserValidateRequest;
use App\Services\UserService;
use App\Services\CardTransactionServices;


class UserController extends Controller
{
    protected $userService;
    protected $cardTransactionServices;
     
    public function __construct(UserService $userService ,
    CardTransactionServices $cardTransactionServices  ){

        $this->userService = $userService;
        $this->cardTransactionServices = $cardTransactionServices;
    }

    public function index()
    {
       $AllUsers = $this->userService->getAllUsers();
       if($AllUsers){
        return response()->json([
            'code'=>200,
            'message'=>'Get All users Successfully',
            'data'=>[
                $AllUsers
            ]
            ],200);
        }
       return $AllUsers;
       
    }
    
    public function store(UserValidateRequest $request)
    {
        $validateData = $request->validated();
        $user = $this->userService->createNewUser($validateData);
        return response()->json([
            'code'=>201,
            'message'=>'Create user Successfully',
            'data'=>[
                'User'=>$user
            ]
        ],201);
    }

    public function show($id)
    {
        $user = $this->userService->getSpecificUser($id);
        return response()->json([
            'code'=>200,
            'message'=>'Get  user Successfully',
            'data'=>[
                'user'=>$user
            ]
        ],200);
    }

    public function update(UpdateUserValidateRequest $request, $id)
    {
        $user = $this->userService->updateUser($id);
        $allRequestData = $request->validated();
        $user->update($allRequestData);
        $user->save();
        return response()->json([
            'card'=>200,
            'message'=>'user Updated successfully',
            'data'=>[
                'user'=>$user
            ]
        ],200);
    }

    public function destroy($id)
    {
        $deleteUser = $this->userService->deleteUser($id);
        if($deleteUser){
             $deleteUser->delete();
             return response()->json([
                'code'=>200,
                'message'=>'delete user Successfully '
            ],200);
        }
    }

    public function profile(){
        $user = auth()->user();
        $card = $this->userService->profile($user->id);
        $cardTransaction = $this->cardTransactionServices->findCardTransactionByCardID($card);
        if(!$cardTransaction){
            $type = "Exit";
        }
        else {
            $type = $cardTransaction->type;
        }
        return response()->json([
            'code'=>200,
            'message'=>'Successfully',
            'data'=>[
            'Full Name'=>$user->first_name.' '.$user->last_name,
            'Card code'=>$card->code,
            'Gender'=>$user->gender,
            'Type'=>$type,
            'Year'=>$user->year,
            'specialization'=>$user->specialization,
            ]
        ],200);
    }
}
