<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Auth;
use Illuminate\Pagination\Paginator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Blade role directives: @role('admin'), @endrole; @hasanyrole(['admin','owner'])
        Blade::directive('role', function ($role) {
            return "<?php if(auth()->check() && auth()->user()->role == {$role}): ?>";
        });

        Blade::directive('endrole', function () {
            return '<?php endif; ?>';
        });

        Blade::directive('hasanyrole', function ($roles) {
            return "<?php if(auth()->check() && in_array(auth()->user()->role, {$roles})): ?>";
        });

        Blade::directive('endhasanyrole', function () {
            return '<?php endif; ?>';
        });

        // Use Tailwind pagination templates so ->links() outputs Tailwind markup
        Paginator::useTailwind();
    }
}
