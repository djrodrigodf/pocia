<?php

namespace App\Livewire\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use LdapRecord\Connection;
use Livewire\Component;
use Mary\Traits\Toast;

class Login extends Component
{
    use Toast;

    public $title = 'POC IA';
    public ?string $email;
    public ?string $password;

    public function authenticate()
    {
        $this->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        try {
            Log::info("Tentando conectar ao LDAP para {$this->email}");

            $config = [
                'hosts'            => [env('LDAP_HOST')],
                'base_dn'          => env('LDAP_BASE_DN'),
                'username'         => env('LDAP_USERNAME'),
                'password'         => env('LDAP_PASSWORD'),
                'port'             => env('LDAP_PORT', 389),
                'use_ssl'          => env('LDAP_SSL', false),
                'use_tls'          => env('LDAP_TLS', false),
                'timeout'          => env('LDAP_TIMEOUT', 5),
                'follow_referrals' => false,
            ];

            $connection = new Connection($config);
            $connection->connect();

            $user = $connection->query()
                ->where('userPrincipalName', '=', $this->email)
                ->orWhere('mail', '=', $this->email)
                ->first();

            if (!$user) {
                Log::warning("Usuário não encontrado no LDAP: {$this->email}");
                $this->error("Usuário não encontrado.", position: 'toast-top');
                return;
            }

            if ($connection->auth()->attempt($user['dn'], $this->password)) {
                Log::info("Usuário autenticado no LDAP: {$this->email}");

                // Cria ou atualiza o usuário local
                $localUser = User::updateOrCreate(
                    ['email' => $this->email],
                    [
                        'name' => $user['cn'][0] ?? $this->email,
                        'email' => $this->email,
                        'password' => bcrypt($this->password),
                    ]
                );

                Auth::login($localUser);
                request()->session()->regenerate();

                $this->success('Login efetuado com sucesso!', position: 'toast-top');
                return Redirect::route('home');
            } else {
                Log::warning("Senha incorreta para: {$this->email}");
                $this->error("Senha incorreta.", position: 'toast-top');
            }

        } catch (\Exception $e) {
            Log::error("Erro no processo de login: {$e->getMessage()}");
            $this->error("Erro ao autenticar. Tente novamente.", position: 'toast-top');
        }
    }

    public function render()
    {
        return view('livewire.auth.login')->layout('components.layouts.applogin');
    }
}

