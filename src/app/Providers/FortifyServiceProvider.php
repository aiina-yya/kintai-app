<?php

namespace App\Providers;

use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;
use App\Http\Responses\LoginResponse;
use App\Models\Admin;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Actions\Fortify\CreateNewUser;
use App\Actions\Fortify\ResetUserPassword;
use App\Actions\Fortify\UpdateUserPassword;
use App\Actions\Fortify\UpdateUserProfileInformation;
use App\Http\Requests\LoginRequest;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Laravel\Fortify\Fortify;
use Laravel\Fortify\Contracts\RegisterResponse;
use Laravel\Fortify\Http\Requests\LoginRequest as FortifyLoginRequest;

class FortifyServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->instance(RegisterResponse::class, new class implements RegisterResponse {
            public function toResponse($request)
            {
                return redirect('/mypage/profile');
            }
        });

        $this->app->singleton(
            LoginResponseContract::class,
            LoginResponse::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Fortify::createUsersUsing(CreateNewUser::class);
        Fortify::registerView(function () {
                return view('auth.register');
        });

        Fortify::loginView(function (Request $request) {
            if ($request->is('admin/*') || $request->is('admin')) {
                return view('admin.login');
            }
            return view ('auth.login');
        });

        Fortify::authenticateUsing(function (Request $request) {

            if (config('fortify.guard') === 'admin') {
                $admin = Admin::where('email', $request->email)->first();

                if ($admin && Hash::check($request->password, $admin->password)) {
                    return $admin;
                }
                return null;
            }


            $user = User::where('email', $request->email)->first();

            if ($user && Hash::check($request->password, $user->password)) {
                return $user;
            }

                return null;
        });

        Fortify::verifyEmailView(function () {
            return view('auth.verify-email');
        });

        RateLimiter::for('login', function (Request $request) {
            $email = (string) $request->email;

            return Limit::perMinute(10)->by($email . $request->ip());
        });

        app()->bind(FortifyLoginRequest::class, LoginRequest::class);
    }
}