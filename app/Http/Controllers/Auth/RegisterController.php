<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

// class RegisterController extends Controller
// {
//     public function showRegistrationForm()
//     {
//         return view('register');
//     }

//     public function register(Request $request)
//     {
//         $request->validate([
//             'username' => 'required|string|max:255|unique:users',
//             'email' => 'required|string|email|max:255|unique:users',
//             'password' => ['required', 'confirmed', Password::min(8)],
//         ]);

//         $user = User::create([
//             'username' => $request->username,
//             'email' => $request->email,
//             'password' => $request->password, 
//             'role' => 'member',
//         ]);

//         return response()->json([
//             'message' => 'User registered successfully',
//             'user' => [
//                 'id' => $user->id,
//                 'username' => $user->username,
//                 'email' => $user->email,
//             ]
//         ], 201); 
//     }

// }

class RegisterController extends Controller
{
    public function showRegistrationForm()
    {
        return view('register'); 
    }

    public function register(Request $request)
    {
        $request->validate([
            'username' => 'required|string|max:255|unique:users',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        $user = User::create([
            'username' => $request->username,
            'email' => $request->email,
            'password' => $request->password, 
        ]);

    }
}