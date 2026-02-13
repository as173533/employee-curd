<?php

namespace App\Livewire\Auth;

use App\Models\User;
use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Illuminate\Support\Facades\RateLimiter;

#[Layout('components.layouts.auth')]
class Login extends Component
{
    const MAX_DEVICES = 2;

    #[Validate('required|string|email')]
    public $email = '';

    #[Validate('required|string')]
    public $password = '';

    public $otp = '';

    public $otpSent = false;
    public $isStaff = false;
    public $remember = false;

    public $resendAvailableAt = 0;

    public $activeDevices = [];
    public $showDeviceManager = false;

    /*
    |--------------------------------------------------------------------------
    | Detect staff/customer
    |--------------------------------------------------------------------------
    */
    public function detectUserType()
    {
        if (!filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
            $this->isStaff = false;
            return;
        }

        $user = User::where('email', $this->email)->first();
        $this->isStaff = $user?->user_type === 'staff';
    }

    /*
    |--------------------------------------------------------------------------
    | Send OTP
    |--------------------------------------------------------------------------
    */
    public function sendOtp()
    {
        $this->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $this->ensureIsNotRateLimited();

        if ($this->resendAvailableAt > now()->timestamp) {
            return;
        }

        $user = User::where('email', $this->email)->first();

        if (!$user || !Hash::check($this->password, $user->password)) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'email' => 'Invalid credentials',
            ]);
        }

        DB::table('user_otps')->where('user_id', $user->id)->delete();

        $otp = rand(100000, 999999);

        DB::table('user_otps')->insert([
            'user_id' => $user->id,
            'otp' => $otp,
            'expires_at' => now()->addMinutes(5),
            'is_verified' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        logger("OTP: " . $otp);

        $this->otpSent = true;
        $this->resendAvailableAt = now()->addSeconds(60)->timestamp;

        RateLimiter::clear($this->throttleKey());
    }

    /*
    |--------------------------------------------------------------------------
    | Load Active Devices
    |--------------------------------------------------------------------------
    */
    private function loadActiveDevices($userId)
    {
        $session_timeout = (config('session.lifetime') ?? 1) * 60;
        $timeout_time = time() - $session_timeout;

        $this->activeDevices = DB::table('sessions')
            ->where('user_id', $userId)
            ->where('last_activity', '>', $timeout_time)
            ->get()
            ->map(function ($s) {
                return [
                    'id' => $s->id,
                    'ip' => $s->ip_address,
                    'agent' => $s->user_agent,
                    'last' => date('d M Y H:i', $s->last_activity),
                ];
            })
            ->toArray();
    }

    /*
    |--------------------------------------------------------------------------
    | Logout specific device
    |--------------------------------------------------------------------------
    */
    public function logoutDevice($sessionId)
    {
        DB::table('sessions')->where('id', $sessionId)->delete();

        $this->dispatch('toast', message: 'Device logged out successfully. Please login again.');

        $user = User::where('email', $this->email)->first();
        if ($user) {
            $this->loadActiveDevices($user->id);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Login
    |--------------------------------------------------------------------------
    */
    public function login()
    {
        $this->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $this->ensureIsNotRateLimited();

        $user = User::where('email', $this->email)->first();

        if (!$user) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'email' => 'User not found',
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | OTP
        |--------------------------------------------------------------------------
        */
        if (!$this->isStaff) {

            if (!$this->otpSent) {
                throw ValidationException::withMessages([
                    'email' => 'Please request OTP first.',
                ]);
            }

            DB::table('user_otps')
                ->where('expires_at', '<', now())
                ->delete();

            $otp = DB::table('user_otps')
                ->where('user_id', $user->id)
                ->where('otp', $this->otp)
                ->where('expires_at', '>', now())
                ->where('is_verified', false)
                ->first();

            if (!$otp) {
                RateLimiter::hit($this->throttleKey());

                throw ValidationException::withMessages([
                    'otp' => 'Invalid or expired OTP',
                ]);
            }

            DB::table('user_otps')
                ->where('id', $otp->id)
                ->update(['is_verified' => true]);
        }

        /*
        |--------------------------------------------------------------------------
        | DEVICE LIMIT
        |--------------------------------------------------------------------------
        */
        $session_timeout = (config('session.lifetime') ?? 1) * 60;
        $timeout_time = time() - $session_timeout;

        $activeSessions = DB::table('sessions')
            ->where('user_id', $user->id)
            ->where('last_activity', '>', $timeout_time)
            ->count();

        if ($activeSessions >= self::MAX_DEVICES) {
            $this->loadActiveDevices($user->id);

            throw ValidationException::withMessages([
                'device_limit' => 'Maximum ' . self::MAX_DEVICES . ' devices allowed. Please logout one.',
            ]);
        }
        


        /*
        |--------------------------------------------------------------------------
        | AUTH
        |--------------------------------------------------------------------------
        */
        if (!Auth::attempt([
            'email' => $this->email,
            'password' => $this->password,
        ], $this->remember)) {

            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'email' => 'Invalid credentials',
            ]);
        }

        RateLimiter::clear($this->throttleKey());

        session()->regenerate();

        $this->redirect(route('dashboard'), navigate: true);
    }

    protected function ensureIsNotRateLimited(): void
    {
        if (!RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'email' => "Too many attempts. Try again in {$seconds} seconds.",
        ]);
    }

    protected function throttleKey(): string
    {
        return strtolower($this->email) . '|' . request()->ip();
    }

    public function render()
    {
        return view('livewire.auth.login');
    }
}
