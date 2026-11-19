<?php

namespace App\Http\Controllers;

use Illuminate\support\facades\Hash;
use App\Models\User;
use Dotenv\Exception\ValidationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class SettingsController extends Controller
{
    public function settings()
    {
        return view('pages.user_profile.settings');
    }

    /**
     * Updates the user's email.
     *
     * This function handles the email change process for a user. It validates the current and new email,
     * checks if the current email exists in the database, verifies if the new email is unique, and updates
     * the user's email if all validations pass.
     *
     * @param Request $request The incoming request containing the current and new email.
     * @return \Illuminate\Http\JsonResponse The response indicating success or failure of the email update.
     *
     * @throws \Illuminate\Validation\ValidationException If the request parameters fail validation.
     */
    public function changeUserEmail(Request $request)
    {
        $currentEmail = $request->currentEmail;
        $newEmail = $request->newEmail;

        //validate request
        $validator = Validator::make($request->all(), [
            'currentEmail' => 'required|email',
            'newEmail' => 'required|email'
        ]);

        // Check if validation failed
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first()
            ]);
        }

        //check if current user email exists
        $record = User::where('email', $currentEmail)->first();
        if (!$record) {
            return response()->json([
                'success' => false,
                'message' => 'Incorrect Current Email!',
            ]);
        }

        //check if the new user email exists
        $checkIfNewEmailExists = User::where('email', $newEmail)->first();
        if ($checkIfNewEmailExists) {
            return response()->json([
                'success' => false,
                'message' => 'New User Email already Exists!',
            ]);
        }

        //save new email
        $record->email = $newEmail;
        $record->save();
        return response()->json([
            'success' => true,
            'message' => 'Email Updated!'
        ]);
    }

    /**
     * Updates the user's password.
     *
     * This function handles the password change process for a user. It validates the current and new password,
     * checks if the current password matches the user's existing password using proper hashing,
     * and updates the user's password with proper encryption.
     *
     * @param Request $request The incoming request containing the current and new password.
     * @return \Illuminate\Http\JsonResponse The response indicating success or failure of the password update.
     */
    public function changeUserPassword(Request $request)
    {
        $currentPassword = $request->currentPassword;
        $newPassword = $request->newPassword;

        //validates the passwords
        $validator = Validator::make($request->all(), [
            'currentPassword' => 'required|min:5',
            'newPassword' => 'required|min:5|confirmed',
        ]);

        // Check if validation failed
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first()
            ]);
        }

        // Gets the current user
        $record = Auth::user();
        if (!$record) {
            return response()->json([
                'success' => false,
                'message' => 'User not authenticated!',
            ]);
        }

        // Check if current password matches using Hash::check
        if (!Hash::check($currentPassword, $record->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Incorrect Current Password!',
            ]);
        }

        // Check if new password is the same as current password
        if (Hash::check($newPassword, $record->password)) {
            return response()->json([
                'success' => false,
                'message' => 'New password must be different from current password!',
            ]);
        }

        // Save new password (working)
        $record->password = Hash::make($newPassword);
        $record->save(); // if this shows an error its not, its the php language server version.
        //do not consider as a error!!!

        return response()->json([
            'success' => true,
            'message' => 'Password Updated!'
        ]);
    }
}
