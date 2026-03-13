<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class UserProfile extends Component
{
    use WithFileUploads;

    public $user;
    
    // Profile Information
    public $name;
    public $email;
    public $phone;
    public $avatar;
    public $new_avatar;
    
    // Password Change
    public $current_password;
    public $new_password;
    public $new_password_confirmation;
    
    // Preferences
    public $notification_email = true;
    public $notification_sms = false;
    public $notification_push = true;
    public $language = 'en';
    public $timezone = 'Asia/Kolkata';
    public $theme = 'light';
    
    // Two Factor Authentication
    public $two_factor_enabled = false;
    
    public $activeTab = 'profile';

    protected $queryString = ['activeTab'];

    public function mount()
    {
        $this->user = Auth::user();
        $this->fillUserData();
    }

    public function fillUserData()
    {
        $this->name = $this->user->name;
        $this->email = $this->user->email;
        $this->phone = $this->user->phone;
        $this->avatar = $this->user->avatar;
        
        // Load preferences from user settings
        $settings = $this->user->settings ?? [];
        $this->notification_email = $settings['notification_email'] ?? true;
        $this->notification_sms = $settings['notification_sms'] ?? false;
        $this->notification_push = $settings['notification_push'] ?? true;
        $this->language = $settings['language'] ?? 'en';
        $this->timezone = $settings['timezone'] ?? 'Asia/Kolkata';
        $this->theme = $settings['theme'] ?? 'light';
        $this->two_factor_enabled = $this->user->two_factor_enabled ?? false;
    }

    public function updateProfile()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users')->ignore($this->user->id)],
            'phone' => 'nullable|string|max:20',
        ]);

        try {
            $this->user->update([
                'name' => $this->name,
                'email' => $this->email,
                'phone' => $this->phone,
            ]);

            session()->flash('profile_success', 'Profile updated successfully!');
        } catch (\Exception $e) {
            session()->flash('profile_error', 'Failed to update profile: ' . $e->getMessage());
        }
    }

    public function updateAvatar()
    {
        $this->validate([
            'new_avatar' => 'required|image|max:2048', // 2MB max
        ]);

        try {
            // Delete old avatar if exists
            if ($this->user->avatar) {
                Storage::delete($this->user->avatar);
            }

            // Store new avatar
            $path = $this->new_avatar->store('avatars', 'public');

            $this->user->update([
                'avatar' => $path,
            ]);

            $this->avatar = $path;
            $this->new_avatar = null;

            session()->flash('avatar_success', 'Avatar updated successfully!');
        } catch (\Exception $e) {
            session()->flash('avatar_error', 'Failed to update avatar: ' . $e->getMessage());
        }
    }

    public function removeAvatar()
    {
        try {
            if ($this->user->avatar) {
                Storage::delete($this->user->avatar);
                $this->user->update(['avatar' => null]);
                $this->avatar = null;
                
                session()->flash('avatar_success', 'Avatar removed successfully!');
            }
        } catch (\Exception $e) {
            session()->flash('avatar_error', 'Failed to remove avatar: ' . $e->getMessage());
        }
    }

    public function updatePassword()
    {
        $this->validate([
            'current_password' => 'required',
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        try {
            // Verify current password
            if (!Hash::check($this->current_password, $this->user->password)) {
                session()->flash('password_error', 'Current password is incorrect.');
                return;
            }

            // Update password
            $this->user->update([
                'password' => Hash::make($this->new_password),
            ]);

            // Clear fields
            $this->current_password = '';
            $this->new_password = '';
            $this->new_password_confirmation = '';

            session()->flash('password_success', 'Password updated successfully!');
        } catch (\Exception $e) {
            session()->flash('password_error', 'Failed to update password: ' . $e->getMessage());
        }
    }

    public function updatePreferences()
    {
        try {
            $settings = [
                'notification_email' => $this->notification_email,
                'notification_sms' => $this->notification_sms,
                'notification_push' => $this->notification_push,
                'language' => $this->language,
                'timezone' => $this->timezone,
                'theme' => $this->theme,
            ];

            $this->user->update([
                'settings' => array_merge($this->user->settings ?? [], $settings),
            ]);

            session()->flash('preferences_success', 'Preferences updated successfully!');
        } catch (\Exception $e) {
            session()->flash('preferences_error', 'Failed to update preferences: ' . $e->getMessage());
        }
    }

    public function enableTwoFactor()
    {
        try {
            // Generate 2FA secret
            $secret = \Google2FA::generateSecretKey();
            
            $this->user->update([
                'two_factor_secret' => encrypt($secret),
                'two_factor_enabled' => true,
            ]);

            $this->two_factor_enabled = true;

            session()->flash('2fa_success', 'Two-factor authentication enabled!');
        } catch (\Exception $e) {
            session()->flash('2fa_error', 'Failed to enable 2FA: ' . $e->getMessage());
        }
    }

    public function disableTwoFactor()
    {
        try {
            $this->user->update([
                'two_factor_secret' => null,
                'two_factor_enabled' => false,
            ]);

            $this->two_factor_enabled = false;

            session()->flash('2fa_success', 'Two-factor authentication disabled!');
        } catch (\Exception $e) {
            session()->flash('2fa_error', 'Failed to disable 2FA: ' . $e->getMessage());
        }
    }

    public function deleteAccount()
    {
        // This should probably have additional confirmation
        // and only soft delete the account
        session()->flash('delete_info', 'Please contact administrator to delete your account.');
    }

    public function render()
    {
        return view('livewire.user-profile', [
            'organizations' => $this->user->organizations()->get(),
        ])->layout('layouts.app');
    }
}