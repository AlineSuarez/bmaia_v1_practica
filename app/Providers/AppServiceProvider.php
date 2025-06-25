<?php
namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;

class AppServiceProvider extends ServiceProvider
{
    public function boot()
    {
        // 1) Configuro el formato global en cuanto antes (view composer)
        View::composer('*', function () {
            if (Auth::check() && Auth::user()->preference && Auth::user()->preference->date_format) {
                config(['app.date_format' => Auth::user()->preference->date_format]);
            } else {
                config(['app.date_format' => 'DD/MM/YYYY']); // Valor por defecto
            }
        });

        // 2) Macro en Carbon que siempre lee la config
        Carbon::macro('userFormat', function ($pattern = null) {
            $fmt = $pattern ?: config('app.date_format', 'DD/MM/YYYY');
            return $this->locale(app()->getLocale())->isoFormat($fmt);
        });

        // 3) Directiva blade
        \Blade::directive('date', function ($exp) {
            return "<?php echo \\Carbon\\Carbon::parse($exp)->userFormat(); ?>";
        });
    }
}
