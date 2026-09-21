<?php

declare(strict_types=1);

namespace Modules\Setting\Providers;

<<<<<<< HEAD
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;

class SettingServiceProvider extends ServiceProvider
{
    protected string $moduleName = 'Setting';
    protected string $moduleNameLower = 'setting';

    /**
     * Boot the application events.
     */
    public function boot(): void
    {
        try {
            $this->registerTranslations();
            $this->registerConfig();
            $this->registerViews();
            $this->registerFactories();
            $this->loadMigrationsFrom(module_path($this->moduleName, 'database/migrations'));
        } catch (\Exception $e) {
            Log::error('Setting module boot error: ' . $e->getMessage());
        }
    }

    /**
     * Register the service provider.
     */
    public function register(): void
    {
        try {
            $this->app->register(RouteServiceProvider::class);
        } catch (\Exception $e) {
            Log::error('Setting module register error: ' . $e->getMessage());
        }
    }

    /**
     * Register config.
     */
    protected function registerConfig(): void
    {
        try {
            $this->publishes([
                module_path($this->moduleName, 'config/config.php') => config_path($this->moduleNameLower . '.php'),
            ], 'config');
            $this->mergeConfigFrom(
                module_path($this->moduleName, 'config/config.php'),
                $this->moduleNameLower
            );
        } catch (\Exception $e) {
            Log::error('Setting config register error: ' . $e->getMessage());
        }
    }

    /**
     * Register views.
     */
    public function registerViews(): void
    {
        try {
            $viewPath = resource_path('views/modules/' . $this->moduleNameLower);
            $sourcePath = module_path($this->moduleName, 'resources/views');

            $this->publishes([
                $sourcePath => $viewPath,
            ], ['views', $this->moduleNameLower . '-module-views']);

            $this->loadViewsFrom(array_merge($this->getPublishableViewPaths(), [$sourcePath]), $this->moduleNameLower);
        } catch (\Exception $e) {
            Log::error('Setting views register error: ' . $e->getMessage());
        }
    }

    /**
     * Register translations.
     */
    public function registerTranslations(): void
    {
        try {
            $langPath = resource_path('lang/modules/' . $this->moduleNameLower);
            $sourcePath = module_path($this->moduleName, 'resources/lang');

            $this->publishes([
                $sourcePath => $langPath,
            ], ['translations', $this->moduleNameLower . '-module-translations']);

            $this->loadTranslationsFrom($sourcePath, $this->moduleNameLower);
        } catch (\Exception $e) {
            Log::error('Setting translations register error: ' . $e->getMessage());
        }
    }

    /**
     * Register an additional location for the views.
    *
     * @return array<int, string>
     */
    protected function getPublishableViewPaths(): array
    {
        $paths = [];
        try {
           /** @var array<int, string> $viewPaths */
            $viewPaths = Config::array('view.paths', []);

            foreach ($viewPaths as $path) {
                $modulePath = $path.'/modules/'.$this->moduleNameLower;

                if (is_dir($modulePath)) {
                    $paths[] = $modulePath;
                }
            }
        } catch (\Exception $e) {
            Log::error('Setting getPublishableViewPaths error: '.$e->getMessage());
        }

        return $paths;
    }

    /**
     * Register factories.
     */
    public function registerFactories(): void
    {
       // No custom factory registration needed.
    }

    /**
     * Get the services provided by the provider.
    *
     * @return array<int, string>
     */
    public function provides(): array
    {
        return [];
    }
=======
use Modules\Xot\Providers\XotBaseServiceProvider;

/**
 * ---.
 */
class SettingServiceProvider extends XotBaseServiceProvider
{
    public string $name = 'Setting';

    protected string $module_dir = __DIR__;

    protected string $module_ns = __NAMESPACE__;
>>>>>>> laraxot/dev
}
