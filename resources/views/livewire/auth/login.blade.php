<div class="d-flex align-items-center justify-content-center vh-100 bg-light"
    x-data="{
        confirmId: null,
        toast: false,
        message: ''
    }"

    x-on:toast.window="
        message = $event.detail.message;
        toast = true;
        setTimeout(() => toast = false, 2000);
    ">

    <div class="card shadow-lg border-0" style="width: 450px;">
        <div class="card-body p-4">

            <h3 class="text-center fw-bold mb-4">
                Login to Your Account
            </h3>
            @error('device_limit')
            

            <div class="mt-3">
                @foreach($activeDevices as $device)
                    <div class="border p-2 mb-2 rounded d-flex justify-content-between">
                        <div>
                            <div><b>IP:</b> {{ $device['ip'] }}</div>
                            <div class="small text-muted">
                                {{ $device['agent'] }}
                            </div>
                            <div class="small">
                                Last active: {{ $device['last'] }}
                            </div>
                        </div>
<!-- 
                        <button
                            wire:click="logoutDevice('{{ $device['id'] }}')"
                            class="btn btn-sm btn-danger"> -->
                        <button
                            @click="confirmId = '{{ $device['id'] }}'"
                            style="color:red; margin-left:5px;"
                        >
                            Logout
                        </button>
                        
                    </div>
                @endforeach
            </div>
            @enderror
            {{-- ================= DELETE CONFIRM ================= --}}
            <div
                x-show="confirmId"
                x-transition
                style="position:fixed; inset:0; background:rgba(0,0,0,.5);"
            >
                <div style="background:white; width:300px; margin:150px auto; padding:20px; border-radius:6px;">
                    <h4>Are you sure?</h4>

                    {{-- DEVICE LIMIT ERROR HERE --}}
                    @error('device_limit')
                        <div class="alert alert-danger py-2 mt-2">
                            {{ $message }}
                        </div>
                    @enderror

                    <div style="margin-top:15px; text-align:right;">
                        <button
                            @click="$wire.logoutDevice(confirmId); confirmId = null"
                            style="color:red;"
                        >
                            Yes, Logout
                        </button>

                        <button @click="confirmId = null">
                            Cancel
                        </button>
                    </div>
                </div>

            </div>



            {{-- ================= TOAST ================= --}}
            <div
                x-show="toast"
                x-transition
                style="position:fixed; top:20px; right:20px; background:black; color:white; padding:10px 15px; border-radius:5px;"
            >
                <span x-text="message"></span>
            </div>



            {{-- GLOBAL ERRORS --}}
            
            @if ($errors->any())
                <div class="alert alert-danger py-2" x-show="!confirmId">
                    @foreach ($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            @endif



            <form
                wire:submit="{{ $isStaff || $otpSent ? 'login' : 'sendOtp' }}"
                x-data="{
                    resendTime: @entangle('resendAvailableAt'),
                    countdown: 0,
                    interval: null,

                    start() {
                        this.update();
                        if(this.countdown > 0) {
                            this.interval = setInterval(() => this.update(), 1000);
                        }
                    },

                    update() {
                        let now = Math.floor(Date.now() / 1000);
                        this.countdown = Math.max(0, this.resendTime - now);

                        if(this.countdown === 0 && this.interval){
                            clearInterval(this.interval);
                            this.interval = null;
                        }
                    }
                }"
                x-init="start()"
            >

                {{-- EMAIL --}}
                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input
                        type="email"
                        class="form-control"
                        wire:model.defer="email"
                        wire:blur="detectUserType"
                        {{ $otpSent && !$isStaff ? 'readonly' : '' }}
                    >
                    @error('email')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>


                {{-- PASSWORD --}}
                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <input
                        type="password"
                        class="form-control"
                        wire:model.defer="password"
                        {{ $otpSent && !$isStaff ? 'readonly' : '' }}
                    >
                </div>


                {{-- OTP --}}
                @if($otpSent && !$isStaff)
                    <div class="mb-3">
                        <label class="form-label">Enter OTP</label>

                        <div class="d-flex justify-content-between mb-1">
                            <small class="text-muted">
                                Please check your email
                            </small>

                            <a href="#"
                               wire:click.prevent="sendOtp"
                               x-show="countdown === 0"
                               class="small">
                                Resend OTP
                            </a>

                            <span x-show="countdown > 0" class="small text-muted">
                                Resend in <span x-text="countdown"></span>s
                            </span>
                        </div>

                        <input
                            class="form-control"
                            wire:model.defer="otp"
                            maxlength="6"
                        >
                    </div>
                @endif


                {{-- REMEMBER --}}
                <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" wire:model="remember">
                    <label class="form-check-label">
                        Remember me
                    </label>
                </div>
                @if ($errors->has('other_device_login'))
                    <div class="d-flex justify-content-between">
                        <div class="mb-3 form-check">
                            <input type="checkbox" wire:model="forceLogout" class="form-check-input" id="is_force_logout">
                            <label class="form-check-label"
                                for="is_force_logout">{{ __('Force logout to other device') }}</label>
                        </div>
                    </div>
                @endif

                {{-- SUBMIT --}}
                <button type="submit"
                        wire:loading.attr="disabled"
                        class="btn btn-primary w-100">

                    <span wire:loading.remove>
                        {{ $isStaff || $otpSent ? 'Login' : 'Send OTP' }}
                    </span>

                    <span wire:loading>
                        <span class="spinner-border spinner-border-sm me-1"></span>
                        Please wait...
                    </span>
                </button>

            </form>

        </div>
    </div>

</div>
