<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Laravel\Passport\RefreshTokenRepository;
use Exception;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Auth; 
use Laravel\Passport\Client as OClient;
use Illuminate\Support\Facades\Hash;
use Session;
use App\Models\UserVerify;
use Illuminate\Support\Str;
use Mail; 
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Carbon;

class AuthController extends Controller
{
    public $successStatus = 200;
    
    public function login() { 
        if (Auth::attempt(['email' => request('email'), 'password' => request('password')])) { 
            $oClient = OClient::where('password_client', 1)->first();
            return $this->getTokenAndRefreshToken($oClient, request('email'), request('password'));
        } 
        else { 
            return response()->json(['error'=>'Unauthorised'], 401); 
        } 
    }

    public function register(Request $request) { 
        $validator = Validator::make($request->all(), [ 
            'name' => 'required', 
            'email' => 'required|email|unique:users', 
            'password' => 'required', 
            'c_password' => 'required|same:password', 
        ]);
        if ($validator->fails()) { 
            return response()->json(['error'=>$validator->errors()], 401);            
        }
        $password = $request->password;
        $input = $request->all(); 
        $input['password'] = bcrypt($input['password']); 
        $user = User::create($input);
        
        $headertoken = Str::random(10);
        $verifytoken = Str::random(6);
        $expiration = Carbon::now()->addDay(1);
  
        UserVerify::create([
              'user_id' => $user->id,
              'headertoken' => $headertoken,
              'token' => $verifytoken,
              'expiry_date' => $expiration
            ]);
  
        Mail::send('email.emailVerificationEmail', ['token' => $verifytoken], function($message) use($request){
              $message->to($request->email);
              $message->subject('Email Verification Mail');
          });

        $oClient = OClient::where('password_client', 1)->first();
        return $this->getTokenAndRefreshToken($oClient, $user->email, $password);
    }

    public function getTokenAndRefreshToken(OClient $oClient, $email, $password) { 
        $oClient = OClient::where('password_client', 1)->first();
        $http = new Client;
        $response = $http->request('POST', 'http://192.168.8.184:8000/oauth/token', [
            'form_params' => [
                'grant_type' => 'password',
                'client_id' => '2',
                'client_secret' => 'QORMkaqMfHHHMithZHF5qWT9XgfAdotWJbkl3BWT',
                'username' => $email,
                'password' => $password,
                'scope' => '*',
            ],
        ]);
        $result = json_decode((string) $response->getBody(), true);
        return response()->json($result, $this->successStatus);
    }

    // Profile API (GET)
    public function profile(){
        
        $userdata = Auth::user();

        $exparire = $userdata->token()->expires_at;
        $token = auth::user()->token();
        $auth = Auth::check();

        return response()->json([
            "status" => true,
            "message" => "Profile data",
            "data" => $userdata,
            "exparire" => $exparire,
            "token" => $token,
            "auth" => $auth
        ]);
    }

    // Logout API (GET)
    public function logout(){

        $token = auth()->user()->token();

        /* --------------------------- revoke access token -------------------------- */
        $token->revoke();
        // $token->delete();

        /* -------------------------- revoke refresh token -------------------------- */
        $refreshTokenRepository = app(RefreshTokenRepository::class);
        $refreshTokenRepository->revokeRefreshTokensByAccessTokenId($token->id);

        return response()->json([
            "status" => true,
            "message" => "User logged out"
        ]);
    }

    public function verifyAccount(Request $request)
    {
        $headertoken = $request->header('ValiditiToken');
        $veridytoken = $request->token;
        $verifyUser = UserVerify::where('token', $veridytoken)->first();
        $currentTime = Carbon::now();

        // return response()->json(['error'=>'Sorry your email cannot be identified.',$verifyUser], 401); 
  
        if(!is_null($verifyUser) && UserVerify::where('headertoken', $headertoken)->first() && !$currentTime->gt($verifyUser->expiry_date)){

            $user = $verifyUser->user;
              
            if(!$user->is_email_verified) {
                $verifyUser->user->is_email_verified = 1;
                $verifyUser->user->save();
                return response()->json([
                    "status" => true,
                    "message" => "Your e-mail is verified. You can now login."
                ]);
            } else {
                return response()->json(['error'=>'Your e-mail is already verified. You can now login.'], 401); 
            }
        }
    }
}
