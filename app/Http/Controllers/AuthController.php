<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
use App\Models\User;
use DB;

class AuthController extends Controller
{
    //

    public function register(Request $request){
        try{
            DB::beginTransaction();
            $validator = Validator::make($request->all(),
                [
                    'name' => 'required',
                    'email' => 'required|email|unique:users',
                    'password' => 'required'
                ],
                [
                    'name.required' => 'Please enter your name',
                    'email.required' => 'Please enter your email',
                    'email.email' => 'Please enter valid email',
                    'email.unique' => 'Email already exist',
                    'password.required' => 'Please enter your password',
                ]);
            if ($validator->fails()) {
                $response['status'] = 'error';
                $response['response'] = $validator->errors();
                return response()->json($response, 403);
            }
            User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => \Hash::make($request->password)
            ]);
            DB::commit();
            $response['status'] = 'Success';
            $response['response'] = "User created successfully";
            return response()->json($response, 200);
        } catch(\Exception $e){
            DB::rollback();
            $response['status'] = 'error';
            $response['response'] = "something went wrong. please try again later";
            \Log::error($e->getMessage());
            return response()->json($response, 503);
        }
    }

    public function login(Request $request){
        try {
            $validator = Validator::make($request->all(),
                [
                    'email' => 'required|email',
                    'password' => 'required'
                ],
                [
                    'email.required' => 'Please enter your email',
                    'email.email' => 'Please enter valid email',
                    'password.required' => 'Please enter your password',
                ]);
            if ($validator->fails()) {
                $response['status'] = 'error';
                $response['response'] = $validator->errors();
                return response()->json($response, 403);
            }

            $input['email'] = $request->email;
            $input['password'] = $request->password;

            $token = null;

            if (!$token = auth()->attempt($input)) {
                $response['status'] = 'error';
                $response['response'] = 'Invalid Email or Password';
                return response()->json($response, 403);
            }
            $response['status'] = 'success';
            $response['response']['user_data'] = auth()->user();
            $response['response']['access_token'] = $token;
            return response()->json($response, 200);

        } catch (\Exception $e) {
            $response['status'] = 'error';
            $response['response'] = 'Something went wrong. Please try again later.';
            \Log::error($e->getMessage());
            return response()->json($response, 503);
        }
    }

    public function logout(){
        try{
            auth()->logout();
            $response['status'] = 'success';
            $response['response'] = 'Logout Successfully';
            return response()->json($response, 200);
        } catch (\Exception $e) {
            $response['status'] = 'error';
            $response['response'] = 'Something went wrong. Please try again later.';
            \Log::error($e->getMessage());
            return response()->json($response, 503);
        }
    }
}
