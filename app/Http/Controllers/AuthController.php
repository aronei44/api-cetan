<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Resources\UserResource;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email'=>'required',
            'password'=>'required|min:8',
        ]);
        $user = User::where('email',$request->email)->first();
        if(!$user){
            return response()->json([
                'message'=>'User not found',
            ],404);
        }else{
            if(\Hash::check($request->password,$user->password)){
                $token = $user->createToken('authToken')->plainTextToken;
                $user->token = $token;
                $user->status = 200;
                return new UserResource($user);
            }else{
                return response()->json([
                    'message'=>'Password not match',
                ],404);
            }
        }
    }
    public function register(Request $request)
    {
        $request->validate([
            'name'=>'required',
            'email'=>'required|email:dns|unique:users',
            'password'=>'required|min:8',
        ]);
        try {
            $user = User::create([
                'name'=>$request->name,
                'email'=>$request->email,
                'password'=>\Hash::make($request->password),
            ]);
            $token = $user->createToken('authToken')->plainTextToken;
            $user->token = $token;
            return new UserResource($user);
        } catch (\Throwable $th) {
            return response()->json([
                'message'=>'something error',
            ],500);
        }
    }
    public function logout(Request $request){

        try {
            $tokens = \DB::select("DELETE FROM personal_access_tokens WHERE tokenable_id = " .$request->user()->id);
            return response()->json([
                'message'=>'Success Deleting Token'
            ],200);
        } catch (\Throwable $th) {
            return response()->json([
                'message'=>'Unable to Deleting Token'
            ],400);
        }
    }
    public function getUser(Request $request){
        return new UserResource($request->user());
    }
}
