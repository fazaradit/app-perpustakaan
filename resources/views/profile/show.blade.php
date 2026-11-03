@extends('layouts.app')

@section('title', 'Profil Pengguna')

@section('content')
<div class="header">
    <h1>Profil Pengguna</h1>
</div>

<div class="card" style="margin-bottom: 20px; padding: 20px; border: 1px solid #ccc; border-radius: 8px;">
    <h2>Data Diri</h2>
    <p><strong>Nama:</strong> {{ $user->name }}</p>
    <p><strong>Email:</strong> {{ $user->email }}</p>
    <p><strong>Role:</strong> {{ ucfirst($user->role) }}</p>
</div>

<div class="card" style="padding: 20px; border: 1px solid #ccc; border-radius: 8px;">
    <h2>Ganti Password</h2>

    @if (session('success'))
        <div style="color: green; margin-bottom: 15px;">{{ session('success') }}</div>
    @endif

    <form action="{{ route('profile.password.update') }}" method="POST">
        @csrf
        @method('PUT')

        <div class="form-group" style="margin-bottom: 15px;">
            <label for="current_password" style="display: block; margin-bottom: 5px;">Password Saat Ini</label>
            <input type="password" name="current_password" id="current_password" required style="width: 100%; padding: 8px;">
            @error('current_password')
                <div style="color: red; margin-top: 5px;">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group" style="margin-bottom: 15px;">
            <label for="new_password" style="display: block; margin-bottom: 5px;">Password Baru</label>
            <input type="password" name="new_password" id="new_password" required style="width: 100%; padding: 8px;">
            @error('new_password')
                <div style="color: red; margin-top: 5px;">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group" style="margin-bottom: 15px;">
            <label for="new_password_confirmation" style="display: block; margin-bottom: 5px;">Konfirmasi Password Baru</label>
            <input type="password" name="new_password_confirmation" id="new_password_confirmation" required style="width: 100%; padding: 8px;">
        </div>

        <button type="submit" class="btn" style="padding: 10px 15px; background: #1e40af; color: white; border: none; border-radius: 4px; cursor: pointer;">Simpan Password Baru</button>
    </form>
</div>
@endsection
