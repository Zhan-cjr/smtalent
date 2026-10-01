<?php

namespace App\Http\Controllers\Hrd;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\AuditLogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class UserController extends Controller
{
    /**
     * Display a listing of system HRD/Interviewer users.
     */
    public function index(Request $request): View
    {
        $query = User::where('role', 'hrd')->latest();

        if ($request->filled('status')) {
            $query->where('is_active', $request->input('status') === '1');
        }

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $users = $query->withCount('conductedInterviews')->paginate(15)->withQueryString();

        $stats = [
            'total_hrd' => User::where('role', 'hrd')->count(),
            'active_hrd' => User::where('role', 'hrd')->where('is_active', true)->count(),
            'inactive_hrd' => User::where('role', 'hrd')->where('is_active', false)->count(),
        ];

        return view('hrd.users.index', [
            'users' => $users,
            'stats' => $stats,
            'statusFilter' => $request->input('status', ''),
            'search' => $request->input('search', ''),
        ]);
    }

    /**
     * Store a newly created HRD/Interviewer user in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', Password::min(6)],
            'phone' => ['nullable', 'string', 'max:30'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => strtolower($validated['email']),
            'password' => Hash::make($validated['password']),
            'role' => 'hrd',
            'phone' => $validated['phone'] ?? null,
            'is_active' => $request->boolean('is_active', true),
        ]);

        AuditLogService::log(
            'CREATE_USER',
            "Menambahkan akun HRD/Interviewer baru: {$user->name} ({$user->email})"
        );

        return redirect()->route('hrd.users.index')->with('success', "Akun HRD {$user->name} berhasil ditambahkan.");
    }

    /**
     * Update the specified HRD user in storage.
     */
    public function update(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'password' => ['nullable', 'string', Password::min(6)],
            'phone' => ['nullable', 'string', 'max:30'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        // Cegah penonaktifan akun sendiri
        if ($user->id === Auth::id() && ! $request->boolean('is_active', true)) {
            return redirect()->route('hrd.users.index')
                ->with('error', 'Anda tidak dapat menonaktifkan akun Anda sendiri yang sedang digunakan.');
        }

        $updateData = [
            'name' => $validated['name'],
            'email' => strtolower($validated['email']),
            'role' => 'hrd',
            'phone' => $validated['phone'] ?? null,
            'is_active' => $request->boolean('is_active', true),
        ];

        if (! empty($validated['password'])) {
            $updateData['password'] = Hash::make($validated['password']);
        }

        $user->update($updateData);

        AuditLogService::log(
            'UPDATE_USER',
            "Memperbarui data akun HRD: {$user->name} ({$user->email})"
        );

        return redirect()->route('hrd.users.index')->with('success', "Data akun HRD {$user->name} berhasil diperbarui.");
    }

    /**
     * Remove the specified user from storage.
     */
    public function destroy(User $user): RedirectResponse
    {
        if ($user->id === Auth::id()) {
            return redirect()->route('hrd.users.index')
                ->with('error', 'Anda tidak dapat menghapus akun Anda sendiri yang sedang aktif.');
        }

        $userName = $user->name;
        $userEmail = $user->email;

        $user->delete();

        AuditLogService::log(
            'DELETE_USER',
            "Menghapus pengguna: {$userName} ({$userEmail})"
        );

        return redirect()->route('hrd.users.index')->with('success', "Pengguna {$userName} berhasil dihapus.");
    }
}
