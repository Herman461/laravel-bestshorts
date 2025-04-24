<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;
use Carbon\Carbon;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\URL;

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

//    public function boot(): void
//    {
//        VerifyEmail::createUrlUsing(function ($notifiable) {
//            $frontendUrl = 'http://localhost:3000/verify-email';
//
//            $verifyUrl = URL::temporarySignedRoute(
//                'verification.verify',
//                Carbon::now()->addMinutes(Config::get('auth.verification.expire', 360)),
//                [
//                   'id' => $notifiable->getKey(),
//                    'hash' => sha1($notifiable->getEmailForVerification()),
//                ]
//            );
////                Carbon::now()->addMinutes(Config::get('auth.verification.expire', 60)),
////                [
////                    'id' => $notifiable->getKey(),
////                    'hash' => sha1($notifiable->getEmailForVerification()),
////                ]
////            );
//
//            return urldecode($verifyUrl);
//        });
//    }
//http://127.0.0.1:8000/api/email/verify/31/f552176a34f96a68fb49289e0fbe142de7a67b03?expires=1727717591&signature=86628a87fdb82464a6c08b921c853b4bbb4f8457aa58884a3468e9fda1146eec
}
