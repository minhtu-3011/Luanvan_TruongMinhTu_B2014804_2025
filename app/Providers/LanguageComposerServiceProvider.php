<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Repositories\Interfaces\LanguageRepositoryInterface as LanguageRepository;

class LanguageComposerServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Bind interface với implementation (nếu bạn chưa bind trong AppServiceProvider)
        $this->app->bind(
            'App\Repositories\Interfaces\LanguageRepositoryInterface',
            'App\Repositories\LanguageRepository'
        );
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        View::composer('backend.dashboard.component.nav', function ($view) {
            // Resolve repository từ container
            $languageRepository = app(LanguageRepository::class);

            $languages = $languageRepository->all();

            $view->with('language', $languages);
        });
    }
}
