<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;


class PasswordChangeController extends Controller
{
    public function Changepassword(Request $request)
    {

    $validator=Validator::make($request->all(),[
        'password'=>'required|min:6'
    ]);
    if($validator->fails())
    {
        return response()->json($validator->errors(), 422);
    }
        $user=auth()->user();
        if($user->is_manual==1){
            return response()->json(['error'=>'Method not allowed']);
        }
        if (Hash::check($request->password, $user->password))
        {
            return response()->json(['error'=>'Please choose a different password']);
        }
        $user->password=bcrypt($request->password);
        $user->save();
        return response()->json(['message'=>'Password changed successfully']);


    

    }

}