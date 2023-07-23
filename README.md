
# Keycloak Middleware

## step #1
install package with below code

```code
composer require pickmap/keycloak-middleware
```

## step #2
‍‍
Go to ‍‍‍‍‍‍‍```/config/app.php``` and put ```KeycloakMiddlewareServiceProvider::class``` code

```php
    'providers' => ServiceProvider::defaultProviders()->merge([
        /*
         * Package Service Providers...
         */
        KeycloakMiddlewareServiceProvider::class,
```

## step #3
add this command in terminal in your root of project
```
php artisan vendor:publish --tag=keycloak-middleware
```

than add your keycloak public key in ```/config/keycloak-middleware.php```
```php
return [
    'public_key' => null,
];
```



## step #4
Now you can check keycloak tokens by installing middleware 
‍‍‍```keycloak-middleware``` for your route

```php
Route::get('/', function () {
    dd(request()->all());
})->middleware('keycloak-middleware');
```

## Scope And Role
Also, you can limit the access of users by sending the required scopes or roles 
### " * " means all (role or scope)
```php
Route::group(['prefix' => 'v1','middleware' => 'keycloak-middleware:*,*'],function(){
    #  routes
});

Route::group(['prefix' => 'v1','middleware' => 'keycloak-middleware:admin,*'],function(){
    #  routes
});

Route::group(['prefix' => 'v1','middleware' => 'keycloak-middleware:admin|user,view.profile|send.email'],function(){
    #  routes
});
```
