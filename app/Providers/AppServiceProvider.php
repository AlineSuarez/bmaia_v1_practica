<?php
namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Carbon;

class AppServiceProvider extends ServiceProvider
{
    public function boot()
    {
        Carbon::macro('userFormat', function ($pattern = null) {
            $fmt = $pattern ?: config('app.date_format', config('date.aliases.short'));
            $locale = app()->getLocale();

            // Si el formato es un alias por locale, úsalo
            $localized = config("date.per_locale.{$locale}.{$fmt}");
            if ($localized) {
                $fmt = $localized;
            }

            // Si es alias general
            $aliases = config('date.aliases');
            if (isset($aliases[$fmt])) {
                $fmt = $aliases[$fmt];
            }

            return $this->locale($locale)->isoFormat($fmt);
        });

        // Directiva Blade
        \Blade::directive('date', function ($expression) {
            return "<?php echo (\Carbon\Carbon::parse($expression))->userFormat(); ?>";
        });
    }
}
