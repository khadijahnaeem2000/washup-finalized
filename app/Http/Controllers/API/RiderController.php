<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Hash;

use Validator;
use App\Models\Rider;
use Illuminate\Http\Request;

class RiderController extends Controller
{
    function login(Request $request)
    {
        $rider      = Rider::where('username', $request->email)->first();
        if (!$rider || !Hash::check($request->password, $rider->password)) {
            return response([
                'status'    => 'failed',
                'data'      => 'Incorrect Credentials!',
            ], 404);
        }
    
        $token          = $rider->createToken('rider-token')->plainTextToken;
    
        $response = [
            'status'    => "success",
            'token'     => $token,
            'username'  => $rider->username,
            'rider_id'  => $rider->id
        ];
        return response($response, 200);
    }

    public function logout(){
        $rider = request()->user(); 
        // Revoke current user token
        $rider->tokens()->where('id', $rider->currentAccessToken()->id)->delete();
        $response = [
            'status'    => "success"
        ];
        return response($response, 200);
    }

    public function fetch_rider(){
        
        $response = Rider::all();
        return response($response, 200);
    }

    public function forgot(Request $request){

        $validator = Validator::make($request->all(),
            [
                'username' => 'required',
            ]
        );

       
        if ($validator->passes()) {

            $chk            = Rider::where('username', $request->username)
                                    ->update([
                                                'forgot'     => '1',
                                            ]);
            if($chk){
                return  response([
                    'status'    => "success"
                ],200);
            }else{
                return  response([
                    'status'    => "failed",
                    'error'     => "username not found!",
                ],404);
            }
        }
        $response = [
            'status'    => "failed",
            'error'     => $validator->errors()->all(),
        ];
        return response($response, 200);
    }


     
}


