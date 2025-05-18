<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ProfileService
{
    public function updateProfile(User $user, array $validatedData): void
    {
        $user->fill($validatedData);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();
    }

    public function deleteAccount(User $user): void
    {
        Auth::logout();

        try {
            $user->delete();
        } catch (\Exception $e) {
            Log::error('Failed to delete user', ['user_id' => $user->id, 'error' => $e->getMessage()]);
            throw new \RuntimeException('Failed to delete account.');
        }
    }
}
