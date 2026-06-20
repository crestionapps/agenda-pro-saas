<?php
namespace App\Http\Controllers;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLogin() { return view('auth.login'); }

    public function login(Request $request)
    {
        $data = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);
        if (auth()->attempt($data, $request->filled('remember'))) {
            $request->session()->regenerate();
            return redirect()->intended($this->redirectTo());
        }
        return back()->withErrors(['email' => 'Credenciais inválidas'])->onlyInput('email');
    }

    public function showRegister() { return view('auth.register'); }

    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6|confirmed',
            'phone' => 'nullable|string',
        ]);
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'phone' => $data['phone'] ?? null,
            'role' => 'customer',
        ]);
        auth()->login($user);
        $request->session()->regenerate();
        return redirect()->route('customer.dashboard');
    }

    public function logout(Request $request)
    {
        auth()->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }

    public function dashboard()
    {
        $user = auth()->user();
        if ($user->isSuperAdmin()) return redirect()->route('platform.admin');
        if ($user->isAdmin()) return redirect()->route('business.admin', $user->tenant_id);
        if ($user->isManager()) return redirect()->route('manager.dashboard', $user->tenant_id);
        return view('dashboard.customer', [
            'appointments' => $user->appointments()->with(['tenant', 'service', 'professional'])->latest()->get(),
        ]);
    }

    private function redirectTo()
    {
        $user = auth()->user();
        if ($user->isSuperAdmin()) return route('platform.admin');
        if ($user->isAdmin()) return route('business.admin', $user->tenant_id);
        if ($user->isManager()) return route('manager.dashboard', $user->tenant_id);
        return route('customer.dashboard');
    }
}
