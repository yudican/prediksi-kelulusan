<div class="login-form">
    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="pl-2">
                <h2 class="text-left"><strong>Daftar akun</strong></h2>
            </div>
            <x-text-field type="text" name="name" label="Nama Lengkap" />
            <x-text-field type="text" name="email" label="Email" />
            <x-text-field type="password" name="password" label="Kata sandi" />
            <x-text-field type="password" name="password_confirmation" label="Konfirmasi kata sandi" />


            <div class="form-group">
                <button type="button" wire:click="store" class="btn btn-secondary w-100 fw-bold"
                    wire:loading.attr="disabled">Daftar</button>
            </div>
            <div class="login-account text-center mt-3">
                <span class="msg">Sudah memiliki akun ?</span>
                <a href="{{ route('login') }}" id="show-signup" class="link">Masuk</a>
            </div>
        </div>
    </div>
</div>