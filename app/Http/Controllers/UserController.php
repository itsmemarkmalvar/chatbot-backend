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

            // Track this visit
            $user->trackVisit();

            // Return user information
            return response()->json([
                'success' => true,
                'name' => $user->name,
                'email' => $user->email,
                'username' => $user->username,
                'is_new_user' => $user->isNewUser(),
                'visit_count' => $user->visit_count,
                'first_visit_at' => $user->first_visit_at,
                'last_visit_at' => $user->last_visit_at,
                'shows_tour' => $user->shows_tour
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to fetch user information'
            ], 500);
        }
    }
    
    public function markTourShown(Request $request)
    {
        try {
            $user = Auth::user();
            
            if (!$user) {
                return response()->json([
                    'error' => 'User not authenticated'
                ], 401);
            }
            
            $user->markTourShown();
            
            return response()->json([
                'success' => true,
                'message' => 'Tour marked as shown'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to update tour status'
            ], 500);
        }
    }
} 