<?php

namespace App\Http\Controllers;


use App\Http\Requests\UpdateUserValidateRequest;
use App\Http\Requests\UserValidateRequest;
use App\Services\UserService;


class UserController extends Controller
{
    protected $userService;
     
    public function __construct(UserService $userService){
        $this->userService = $userService;
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
            ]);
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
        ]);
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
        ]);
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
        ]);
    }

    public function destroy($id)
    {
        $deleteUser = $this->userService->deleteUser($id);
        if($deleteUser){
             $deleteUser->delete();
             return response()->json([
                'code'=>200,
                'message'=>'delete user Successfully '
            ]);
        }
    }
}
