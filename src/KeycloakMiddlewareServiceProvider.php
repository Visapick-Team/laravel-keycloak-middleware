<?php

namespace Pickmap\Keycloak;
use Illuminate\Support\ServiceProvider;

class KeycloakMiddlewareServiceProvider extends ServiceProvider
{

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {    
        $router = $this->app['router'];
        $router->aliasMiddleware('keycloak-middleware', KeycloakMiddleware::class);

        $this->publishes([__DIR__.'/config/keycloak-middleware.php' => config_path('keycloak-middleware.php')], 'keycloak-middleware');
    }
    
    
    /**
     * Register services.
     */
    public function register(): void
    {
 
    }

}
