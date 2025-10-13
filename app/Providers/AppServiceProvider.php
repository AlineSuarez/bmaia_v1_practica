<?php
namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Carbon;

class AppServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // ⚠️ Elimina cualquier View::composer aquí

        // Macro en Carbon
        Carbon::macro('userFormat', function ($pattern = null) {
            $fmt = $pattern ?: config('app.date_format', 'DD/MM/YYYY');
            return $this->locale(app()->getLocale())->isoFormat($fmt);
        });

        // Directiva Blade
        Blade::directive('date', function ($exp) {
            return "<?php echo \\Carbon\\Carbon::parse($exp)->userFormat(); ?>";
        });
    }
}