<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function info(Request $request)
    {
        try {
            // Get the authenticated user
            $user = Auth::user();
            
            if (!$user) {
                return response()->json([
                    'error' => 'User not authenticated'
                ], 401);
            }

            // Return user information
            return response()->json([
                'success' => true,
                'name' => $user->name,
                'email' => $user->email,
                'username' => $user->username
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to fetch user information'
            ], 500);
        }
    }
} 