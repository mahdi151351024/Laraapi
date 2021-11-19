<?php
namespace App\Http\Controllers\Api;
use Illuminate\Http\Request; 
use App\Http\Controllers\Controller; 
use App\Models\User; 
use Tymon\JWTAuth\Exceptions\JWTException;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth; 
use JWTAuth;
use Validator;
class UserController extends Controller 
{
    public function register(Request $request)
    {
    	//Validate data
        $data = $request->only('user_name', 'email', 'password');
        $validator = Validator::make($data, [
            'user_name' => 'required|min:4|max:20|unique:users',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:6|max:50'
        ]);

        //Send failed response if request is not valid
        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()], 200);
        }
        $pin = random_int(100000, 999999);
        //Request is valid, create new user
        $user = User::create([
        	'user_name' => $request->user_name,
        	'email' => $request->email,
        	'password' => bcrypt($request->password),
            'pin' => $pin,
            'user_role' => 'user'
        ]);

        $to_name = $request->user_name;
        $to_email = $request->email;
        $data = ["name" => $request->user_name, "body" => "Your 6 Digit pin: ".$pin];
        \Mail::send("email.pin_template", $data, function($message) use ($to_name, $to_email) {
            $message->to($to_email, $to_name)
            ->subject("6 Digit Pin");
            $message->from("mahdiodesk2015@gmail.com", "6 Digit Pin Mail");
        });

        //User created, return success response
        return response()->json([
            'success' => true,
            'message' => '6 digit pin sent to your email successfully',
            'data' => $user
        ], Response::HTTP_OK);
    }
 
    public function authenticate(Request $request)
    {
        $credentials = $request->only('email', 'password');

        //valid credential
        $validator = Validator::make($credentials, [
            'email' => 'required|email',
            'password' => 'required|string|min:6|max:50'
        ]);

        //Send failed response if request is not valid
        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()], 200);
        }

        //Request is validated
        //Crean token
        try {
            if (! $token = JWTAuth::attempt($credentials)) {
                return response()->json([
                	'success' => false,
                	'message' => 'Login credentials are invalid.',
                ], 400);
            }
        } catch (JWTException $e) {
    	return $credentials;
            return response()->json([
                	'success' => false,
                	'message' => 'Could not create token.',
                ], 500);
        }
 	
 		//Token created, return with success response and jwt token
        return response()->json([
            'success' => true,
            'token' => $token,
        ]);
    }
 
    public function logout(Request $request)
    {
        //valid credential
        $validator = Validator::make($request->only('token'), [
            'token' => 'required'
        ]);

        //Send failed response if request is not valid
        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()], 200);
        }

		//Request is validated, do logout        
        try {
            JWTAuth::invalidate($request->token);
 
            return response()->json([
                'success' => true,
                'message' => 'User has been logged out'
            ]);
        } catch (JWTException $exception) {
            return response()->json([
                'success' => false,
                'message' => 'Sorry, user cannot be logged out'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
 
    public function getUser(Request $request)
    {
        $this->validate($request, [
            'token' => 'required'
        ]);
 
        $user = JWTAuth::authenticate($request->token);
 
        return response()->json(['user' => $user]);
    }

    public function confirmRegister(Request $request)
    {
        $user = User::where('email', $request->email)->where('pin', $request->pin)->first();
        if($user)
        {
            $user->is_registered = 'Yes';
            $user->update();
            return response()->json([
                'status' => true,
                'message' => 'You have been registered successfully',
                'data' => []
            ]);
        }
        else
        {
            return response()->json([
                'status' => false,
                'message' => 'Pin is incorrect',
                'data' => []
            ]);
        }
    }

    public function updateProfile(Request $request)
    {
        $user = User::where('id', $request->user_id)->first();
        $user->name = $request->name;
        $user->email = $request->email;
        if($request->file('avatar'))
        {
            $file_name = time().'.'.$request->file('avatar')->getClientOriginalExtension();  
            $request->file('avatar')->move(public_path('uploads'), $file_name);
            $user->avatar = 'uploads/'.$file_name;
        }
        $user->update();
        return response()->json([
            'status' => true,
            'message' => 'Profile updated successfully',
            'data' => $user
        ]);
        
    }
}
