<?php

namespace App\Http\Controllers;

use App\Models\Tutor;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        return view('pages.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ], [
            'email.required'    => 'O e-mail é obrigatório.',
            'email.email'       => 'Informe um e-mail válido.',
            'password.required' => 'A senha é obrigatória.',
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            return redirect()->intended(route('dashboard'))
                ->with('success', 'Bem-vindo de volta, ' . Auth::user()->name . '!');
        }

        return back()->withErrors([
            'email' => 'Credenciais inválidas. Verifique seu e-mail e senha.',
        ])->withInput($request->only('email'));
    }

    public function showRegister()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        return view('pages.registo');
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name'     => ['required', 'string', 'min:3', 'max:100'],
            'email'    => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'min:8', 'confirmed'],
        ], [
            'name.required'      => 'O nome é obrigatório.',
            'name.min'           => 'O nome deve ter ao menos 3 caracteres.',
            'email.required'     => 'O e-mail é obrigatório.',
            'email.email'        => 'Informe um e-mail válido.',
            'email.unique'       => 'Este e-mail já está cadastrado.',
            'password.required'  => 'A senha é obrigatória.',
            'password.min'       => 'A senha deve ter ao menos 8 caracteres.',
            'password.confirmed' => 'As senhas não conferem.',
        ]);

        $user = User::create([
            'name'     => $validated['name'],
            'email'    => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        $this->criarTutorParaUsuario($user);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('dashboard')
            ->with('success', 'Conta criada com sucesso! Bem-vindo à UniAnimal, ' . $user->name . '!');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('home')->with('success', 'Sessão encerrada com sucesso.');
    }

    /*
    |--------------------------------------------------------------------------
    | Login com Google (Laravel Socialite)
    |--------------------------------------------------------------------------
    */

    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->stateless()->user();
        } catch (\Throwable $e) {
            Log::error('Falha no login com Google: ' . $e->getMessage());

            return redirect()->route('login')
                ->withErrors(['email' => 'Não foi possível autenticar com o Google. Tente novamente.']);
        }

        // 1. Já existe usuário com este google_id
        $user = User::where('google_id', $googleUser->getId())->first();

        // 2. Existe usuário com o mesmo e-mail (cadastro tradicional) -> associa a conta
        if (! $user) {
            $user = User::where('email', $googleUser->getEmail())->first();

            if ($user) {
                $user->update([
                    'google_id' => $googleUser->getId(),
                    'avatar'    => $googleUser->getAvatar(),
                ]);
            }
        }

        // 3. Novo usuário
        if (! $user) {
            $user = User::create([
                'name'      => $googleUser->getName() ?: $googleUser->getNickname(),
                'email'     => $googleUser->getEmail(),
                'password'  => Hash::make(Str::random(32)),
                'google_id' => $googleUser->getId(),
                'avatar'    => $googleUser->getAvatar(),
            ]);
        }

        $this->criarTutorParaUsuario($user);

        Auth::login($user, true);

        return redirect()->intended(route('dashboard'))
            ->with('success', 'Bem-vindo, ' . $user->name . '!');
    }

    /**
     * Garante que todo usuário autenticado possua um Tutor 1:1 associado.
     */
    private function criarTutorParaUsuario(User $user): Tutor
    {
        return $user->tutor ?: Tutor::create([
            'user_id'  => $user->id,
            'nome'     => $user->name,
            'telefone' => '',
            'email'    => $user->email,
        ]);
    }
}
