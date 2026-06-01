<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Couple;
use App\Models\User;
use App\Models\Category;
use App\Models\Bank;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($request->only('email', 'password'), $request->filled('remember'))) {
            $request->session()->regenerate();
            return response()->json(['success' => true, 'redirect' => route('dashboard')]);
        }

        return response()->json(['success' => false, 'message' => 'Email atau password salah!'], 422);
    }

    public function showRegister()
    {
        return view('auth.register', [
            'inviteCode' => strtoupper((string) request('invite', '')),
            'inviteMode' => request('action') === 'join' || request()->filled('invite'),
        ]);
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'couple_action' => ['required', Rule::in(['create', 'join'])],
            'couple_name' => ['required_if:couple_action,create', 'nullable', 'string', 'max:255'],
            'couple_avatar' => ['nullable', 'string', 'max:20'],
            'invite_code' => ['required_if:couple_action,join', 'nullable', 'string', 'size:8'],
            'user_avatar' => ['nullable', 'string', 'max:20'],
        ], [
            'email.unique' => 'Email sudah digunakan.',
            'password.min' => 'Password minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak sama.',
            'couple_name.required_if' => 'Nama pasangan wajib diisi.',
            'invite_code.required_if' => 'Kode undangan wajib diisi.',
            'invite_code.size' => 'Kode undangan harus 8 karakter.',
        ]);

        if ($data['couple_action'] === 'create') {
            $couple = Couple::create([
                'couple_name' => $data['couple_name'],
                'avatar_couple' => $request->couple_avatar ?? '💑',
            ]);

            // Seed default categories
            $this->seedDefaultCategories($couple->id);

            $role = 'owner';
        } else {
            $couple = Couple::where('invite_code', strtoupper($data['invite_code']))->first();
            if (!$couple) {
                return response()->json(['success' => false, 'message' => 'Kode undangan tidak valid!'], 422);
            }
            if ($couple->users()->count() >= 2) {
                return response()->json(['success' => false, 'message' => 'Pasangan sudah penuh (max 2 orang)!'], 422);
            }
            $role = 'partner';
        }

        $user = User::create([
            'couple_id' => $couple->id,
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'avatar' => $request->user_avatar ?? '👤',
            'role' => $role,
        ]);

        Auth::login($user);
        $request->session()->flash('show_onboarding', true);
        return response()->json(['success' => true, 'redirect' => route('dashboard')]);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }

    public function profile()
    {
        return view('profile.index', ['user' => Auth::user()]);
    }

    public function updateProfile(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . Auth::id(),
            'avatar' => 'nullable',
            'profile_photo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ], [
            'profile_photo.max' => 'Ukuran foto profil maksimal 2MB.',
            'profile_photo.mimes' => 'Format foto profil harus JPG, JPEG, atau PNG.',
            'email.unique' => 'Email sudah digunakan.',
        ]);

        $user = Auth::user();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->avatar = $request->avatar ?: $user->avatar;

        if ($request->hasFile('profile_photo')) {
            $folder = public_path('uploads/profiles');
            if (!File::exists($folder)) {
                File::makeDirectory($folder, 0755, true);
            }

            if ($user->profile_photo && File::exists($folder . '/' . $user->profile_photo)) {
                File::delete($folder . '/' . $user->profile_photo);
            }

            $filename = time() . '_' . uniqid() . '.' . $request->file('profile_photo')->getClientOriginalExtension();
            $request->file('profile_photo')->move($folder, $filename);
            $user->profile_photo = $filename;
        }

        $user->save();

        return redirect()->route('profile')->with('success', 'Profil berhasil diperbarui.');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|confirmed|min:8',
        ]);

        $user = Auth::user();
        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Password saat ini salah.']);
        }

        $user->password = Hash::make($request->password);
        $user->save();

        return redirect()->route('profile')->with('success', 'Password berhasil diperbarui.');
    }

    private function seedDefaultCategories(int $coupleId): void
    {
        $categories = [
            ['name' => 'Makanan & Minuman', 'icon' => '🍜', 'color' => '#f97316', 'type' => 'expense'],
            ['name' => 'Transportasi', 'icon' => '🚗', 'color' => '#3b82f6', 'type' => 'expense'],
            ['name' => 'Belanja', 'icon' => '🛍️', 'color' => '#ec4899', 'type' => 'expense'],
            ['name' => 'Hiburan', 'icon' => '🎬', 'color' => '#8b5cf6', 'type' => 'expense'],
            ['name' => 'Kesehatan', 'icon' => '💊', 'color' => '#10b981', 'type' => 'expense'],
            ['name' => 'Tagihan', 'icon' => '📄', 'color' => '#ef4444', 'type' => 'expense'],
            ['name' => 'Pendidikan', 'icon' => '📚', 'color' => '#f59e0b', 'type' => 'expense'],
            ['name' => 'Perawatan Diri', 'icon' => '💆', 'color' => '#06b6d4', 'type' => 'expense'],
            ['name' => 'Date & Liburan', 'icon' => '❤️', 'color' => '#f43f5e', 'type' => 'expense'],
            ['name' => 'Lainnya', 'icon' => '📦', 'color' => '#6b7280', 'type' => 'expense'],
            ['name' => 'Gaji', 'icon' => '💼', 'color' => '#10b981', 'type' => 'income'],
            ['name' => 'Freelance', 'icon' => '💻', 'color' => '#6366f1', 'type' => 'income'],
            ['name' => 'Bisnis', 'icon' => '🏪', 'color' => '#f59e0b', 'type' => 'income'],
            ['name' => 'Hadiah', 'icon' => '🎁', 'color' => '#ec4899', 'type' => 'income'],
            ['name' => 'Lainnya', 'icon' => '💰', 'color' => '#6b7280', 'type' => 'income'],
        ];

        foreach ($categories as $cat) {
            Category::create(array_merge($cat, ['couple_id' => $coupleId, 'is_default' => true]));
        }
    }
}
