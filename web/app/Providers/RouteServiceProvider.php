<?php

namespace App\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * This namespace is applied to your controller routes.
     *
     * In addition, it is set as the URL generator's root namespace.
     *
     * @var string
     */
    protected $namespace      = 'App\Http\Controllers\Api';
    protected $webNamespace   = 'App\Http\Controllers\Admin';
    protected $frontNamespace = 'App\Http\Controllers\Front';
	protected $rongNamespace = 'App\Http\Controllers\Rong';
	protected $tenderNamespace = 'App\Http\Controllers\Tender';
    protected $disNamespace = 'App\Http\Controllers\Disapi';
    protected $openApiNamespace = 'App\Http\Controllers\Openapi';
    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {
        //

        parent::boot();
    }

    /**
     * Define the routes for the application.
     *
     * @return void
     */
    public function map()
    {
        $this->mapApiRoutes();

        $this->mapWebRoutes();

        $this->mapFrontRoutes();

        $this->mapRongRoutes();

        $this->mapTenderRoutes();

        $this->mapDisApiRoutes();

        $this->mapOpenApiRoutes();
    }

    /**
     * Define the "web" routes for the application.
     *
     * These routes all receive session state, CSRF protection, etc.
     *
     * @return void
     */
    protected function mapWebRoutes()
    {
        Route::middleware('web')
             ->prefix("admin")
             ->namespace($this->webNamespace)
             ->group(base_path('routes/web.php'));
    }

    /**
     * Define the "api" routes for the application.
     *
     * These routes are typically stateless.
     *
     * @return void
     */
    protected function mapApiRoutes()
    {
        Route::prefix('api')
             ->middleware(['api'])
             ->namespace($this->namespace)
             ->group(base_path('routes/api.php'));
    }

    /**
     * Define the "front" routes for the application.
     *
     * These routes are typically stateless.
     *
     * @return void
     */
    protected function mapFrontRoutes()
    {
        Route::middleware(['web'])
             ->namespace($this->frontNamespace)
             ->group(base_path('routes/front.php'));
    }

	/**
	 * Define the "rong" routes for the application.
	 *
	 * These routes are typically stateless.
	 *
	 * @return void
	 */
	protected function mapRongRoutes()
	{
		Route::middleware(['web'])
			->prefix('rong')
			->namespace($this->rongNamespace)
			->group(base_path('routes/rong.php'));
	}

	protected function mapTenderRoutes()
	{
		Route::middleware(['web'])
			->prefix('tender')
			->namespace($this->tenderNamespace)
			->group(base_path('routes/tender.php'));
	}

    protected function mapDisApiRoutes()
    {
        Route::middleware(['disapi'])
            ->prefix('disapi')
            ->namespace($this->disNamespace)
            ->group(base_path('routes/disapi.php'));
    }

    protected function mapOpenApiRoutes()
    {
        Route::middleware(['api', 'openapi'])
            ->prefix('openapi')
            ->namespace($this->openApiNamespace)
            ->group(base_path('routes/openapi.php'));
    }
}
