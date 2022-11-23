<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
use App\Models\User;
use DB;

class GeneralController extends Controller
{
    //

    public function add_wallet(Request $request){
        try{
            DB::beginTransaction();
            $validator = Validator::make($request->all(),
            [
                'amount' => 'required|numeric|between:3,100|regex:/^-?[0-9]+(?:\.[0-9]{1,2})?$/',
            ],
            [
                'amount.required' => 'Please enter your amount',
                'amount.numeric' => 'Please enter valid amount',
                'amount.between' => 'Please enter amount between 3 and 100',
                'amount.regex' => "Please enter decimal value upto 2"
            ]);
            if ($validator->fails()) {
                $response['status'] = 'error';
                $response['response'] = $validator->errors();
                return response()->json($response, 403);
            }
            $user = User::where('id', auth()->user()->id)->first();
            $user->wallet += $request->amount;
            $user->save();
            DB::commit();
            $response['status'] = 'Success';
            $response['response'] = "Wallet updated successfully";
            return response()->json($response, 200);
        } catch(\Exception $e){
            DB::rollback();
            $response['status'] = 'error';
            $response['response'] = "something went wrong. please try again later";
            \Log::error($e->getMessage());
            return response()->json($response, 503);
        }
    }

    public function buy_a_cookie(Request $request){
        try{
            DB::beginTransaction();
            $validator = Validator::make($request->all(),
            [
                'cookie' => 'required|integer|min:1'
            ],
            [
                'cookie.required' => 'Please enter cookie value',
                'cookie.integer' => 'Please enter valid values',
                'cookie.min' => "Please enter minimum 1"
            ]);
            if ($validator->fails()) {
                $response['status'] = 'error';
                $response['response'] = $validator->errors();
                return response()->json($response, 403);
            }
            $user = User::where('id', auth()->user()->id)->first();
            if($user->wallet >= $request->cookie){
                $user->wallet -= $request->cookie;
                $user->save();
            }
            else{
                $response['status'] = 'error';
                $response['response'] = "Insufficient funds for purchasing cookie";
                return response()->json($response, 403);
            }

            DB::commit();
            $response['status'] = 'Success';
            $response['response'] = "Cookie purchased successfully";
            return response()->json($response, 200);
        } catch(\Exception $e){
            DB::rollback();
            $response['status'] = 'error';
            $response['response'] = "something went wrong. please try again later";
            \Log::error($e->getMessage());
            return response()->json($response, 503);
        }
    }
}
