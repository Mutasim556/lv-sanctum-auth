<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        //
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        
        ResetPassword::createUrlUsing(function($user,$token){
            $query = http_build_query([
                'email' => $user->email,
                'token' => $token
            ]);
            return 'http://127.0.0.1:8000/api/auth/resetpassword?'.$query;
        });
    }
}
