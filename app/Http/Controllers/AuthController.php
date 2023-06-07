<?php

namespace App\Http\Controllers;

use App\Http\Requests\Loginrequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\UpdateProfileRequest;
use App\Models\User;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;

class AuthController extends Controller
{
    public function register(RegisterRequest $data){

        
        $user = User::create([
            'name' => $data->name,
            'user_name' => $data->user_name,
            'email' => $data->email,
            'password' => Hash::make($data->password)
        ]);

        event(new Registered($user));

        return $user;
    }
    public function login(Loginrequest $data){

        $credentials = $data->only('email','password');
        if(!Auth::attempt($credentials)){
            throw new AuthenticationException(message:'Invalid user information');
        }

        return[
            'message' => 'Successfully login',
            'user' => auth()->user(),
            'token' => auth()->user()->createToken('access-token')->plainTextToken,
        ];
    }

    public function logout(){
        auth()->user()->currentAccessToken()->delete();
        return ['message'=>'successfully loged out'];
    }   

    public function UpdateProfile(UpdateProfileRequest $data){

        $user = auth()->user();
        $user->fill($data->only('name','user_name','email'));

        if($user->isDirty('email')){
            $user->forceFill(['email_verified_at'=>null]);
            $user->sendEmailVerificationNotification();
        }

        $user->save();

        return [
            'message' => 'successfully updated'
        ];
    }

    public function UpdatePassword(Request $data){
        $data->validate([
            'old_password' =>'required|max:255',
            'new_password' => 'required|max:255',
            'password_again' => 'required|same:new_password|max:255',
        ]);

        if(Hash::check($data->old_password,auth()->user()->password)){
            auth()->user()->update([
                'password' => Hash::make($data->new_password),
            ]);
            return [
                'message'=>'Password Changed Successfully'
            ];
        }else{
            return [
                'message'=>'Invalid Password'
            ];
        }
    }

    public function ForgetPassword(Request $data){
        $data->validate([
            'email' => 'required|exists:users,email|max:255',
        ]);

        $status = Password::sendResetLink([
            "email" => $data->email,
        ]);

        if($status==Password::RESET_LINK_SENT){
            return[
                'message'=>'Reset Link Sent Successfully',
            ];
        }
    }

    public function ResetPassword(Request $data){
        $data->validate([
            'email' => 'required|max:255|email',
            'password' => 'required|max:255',
            'password_again' => 'required|same:password',
            'token' => 'required',
        ]);


        $status = Password::reset($data->only('email','token','password'),function($user) use($data){
            $user->update([
                'password' => Hash::make($data->password),
            ]);
        });

        if($status == Password::PASSWORD_RESET){
            return [
                'message' => 'Password Changed Successfully'
            ];
        }else{
            return response()->json([
                'message' => 'Could not change password'
            ],403);
        }


    }

    public function VerifyEmail(Request $data){
        auth()->loginUsingId($data->id);

        if($data->user()->hasVerifiedEmail()){
            return response()->json([
                'messgae' => 'You have already verified your email',
            ],Response::HTTP_FORBIDDEN);
        }

        $data->user()->markEmailAsVerified();
        return redirect(env('APP_URL').'?verified=1');
    }

    public function ResendVerifyEmail(Request $data){
        if($data->user()->hasVerifiedEmail()){
            return response()->json([
                'messgae' => 'You have already verified your email',
            ],Response::HTTP_FORBIDDEN);
        }

        $data->user()->sendEmailVerificationNotification();

        return [
            'message' => 'Verifiaction email resend successfully',
        ];
    }
}
