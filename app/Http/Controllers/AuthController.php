<?php
namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;   
use Illuminate\Http\Request;
use App\Models\User;

use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{

    // Register method
    public function register(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string',
                'email' => 'required|string|email|unique:users',
                'password' => 'required|string|min:8|confirmed'
            ]);

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => bcrypt($request->password)
            ]);

            $modules = DB::table('modules')->pluck('id')->toArray();
            $moduleData = [];
            foreach ($modules as $moduleId) {
                $moduleData[$moduleId] = ['active' => false];
            }
            $user->modules()->attach($moduleData);

            $res = [
                "id" => $user->id,
                "name" => $user->name,
                "email" => $user->email,
                "created_at" => $user->created_at
            ];
            return response()->json($res, 201);

        } catch (ValidationException $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }   

    // Login method
    public function login(Request $request){
         try {
            $request->validate([
            'email' => 'email|required',
            'password' => 'required'
            ]);
            
            $credentials = request(['email', 'password']);
            
            if (!Auth::attempt($credentials)) {
                return response()->json([
                    'message' => 'Unauthorize'
                ],401);
            }
            
            $user = User::where('email', $request->email)->first();
            
            $tokenResult = $user->createToken('authToken')->plainTextToken;
            
            return response()->json([
            'token' => $tokenResult,
            'user_id' => $user->id
            ], 200);
        } catch (Exception $error) {
            return response()->json([
            'message' => 'Error in Login',
            'error' => $error,
            ],400);
        }
    }
    
    // Get user method to retrieve user's info
    public function get_user(Request $request){

        return response()->json($request->user());

    }

    // Fetch user list (excluding current user)
    public function getUsers(Request $request)
    {
        $currentUserId = auth()->id();
        
        $users = User::where('id', '!=', $currentUserId)
            ->select('id', 'name', 'email')
            ->orderBy('name')
            ->get();
        
        return response()->json($users, 200);
    }
}