<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Rider;
use Illuminate\Support\Facades\Hash;
class RegisterController extends Controller
{
    function rider_login(Request $request)
    {
        $rider      = Rider::where('username', $request->email)->first();

        if (!$rider || !Hash::check($request->password, $rider->password)) {
            return response([
                'status' => 'failed',
                'data' => 'Incorrect Credentials!',
            ], 404);
        }
    
        $token          = $rider->createToken('rider-token')->plainTextToken;
    
        $response = [
            'status'    => "success",
            'token'     => $token,
            'username'  => $rider->id
        ];
        return response($response, 201);
    }

    public function rider_logout($token){
        $rider->tokens()->where('token', $tokenId)->delete();
    }

    public function fetch_rider(){
        $response = Rider::all();
        return response($response, 201);
    }

     
}


