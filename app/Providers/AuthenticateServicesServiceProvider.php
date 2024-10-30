<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Opcodes\LogViewer\Facades\LogViewer;

class AuthenticateServicesServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->logViewerAccess();
    }

    public function logViewerAccess()
    {
        LogViewer::auth(function ($request) {
            return $request->user()
                && $request->user()->email == 'jeffrey92.hrb@gmail.com';
        });
    }
}
