<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Candidate;
use App\Models\User;
use App\Models\Vacancy;
use App\Services\AuditLogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showLogin(): View|RedirectResponse
    {
        if (Auth::check()) {
            return redirect()->route('hrd.dashboard');
        }

        return view('auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            $user = Auth::user();
            if (! $user->is_active) {
                Auth::logout();
                $request->session()->invalidate();

                return back()->withErrors(['email' => 'Akun Anda dinonaktifkan. Silakan hubungi HRD.']);
            }

            AuditLogService::log('LOGIN', "User {$user->name} ({$user->role}) berhasil login.");

            if ($user->isHrd()) {
                return redirect()->intended(route('hrd.dashboard'));
            }

            // Kandidat tidak memiliki dashboard — arahkan ke halaman publik
            return redirect()->route('home');
        }

        return back()->withErrors([
            'email' => 'Email atau password yang Anda masukkan salah.',
        ])->onlyInput('email');
    }

    public function showRegister(): View
    {
        $vacancies = Vacancy::where('status', 'open')->with('position')->get();

        return view('auth.register', compact('vacancies'));
    }

    public function register(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'phone' => ['required', 'string', 'max:25'],
            'vacancy_id' => ['required', 'exists:vacancies,id'],
            'nik' => ['nullable', 'string', 'max:20'],
            'gender' => ['required', 'in:L,P'],
            'birth_date' => ['nullable', 'date'],
            'education' => ['nullable', 'string', 'max:100'],
            'address' => ['nullable', 'string'],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'candidate',
            'phone' => $request->phone,
            'is_active' => true,
        ]);

        Candidate::create([
            'user_id' => $user->id,
            'vacancy_id' => $request->vacancy_id,
            'nik' => $request->nik,
            'phone' => $request->phone,
            'gender' => $request->gender,
            'birth_date' => $request->birth_date,
            'education' => $request->education,
            'address' => $request->address,
            'status' => 'REGISTERED',
        ]);

        AuditLogService::log('REGISTER', "Kandidat baru mendaftar: {$user->name}", $user, $request);

        Auth::login($user);

        return redirect()->route('candidate.dashboard')->with('success', 'Pendaftaran berhasil! Selamat datang di portal seleksi.');
    }

    public function logout(Request $request): RedirectResponse
    {
        $user = Auth::user();
        if ($user) {
            AuditLogService::log('LOGOUT', "User {$user->name} logout.");
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Anda telah berhasil logout.');
    }
}
