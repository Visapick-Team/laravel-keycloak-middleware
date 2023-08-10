<?php

namespace Pickmap\Keycloak;

use Closure;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response as HttpResponse;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;
use Pickmap\Responder\Res;

class KeycloakMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next,$scopes = '*',$roles = '*'): Response
    {            
        $validator = Validator::make(
            ['token' => $request->bearerToken()],
            [
                'token' => [
                    'required',
                    new TokenValidator()
                ],
            ]
        );

        if($validator->fails())
        {
            return Res::error('error',$validator->errors(),HttpResponse::HTTP_UNAUTHORIZED);
        }

        // You do not have access
        if ($this->accessDenied($request,$scopes,$roles)) 
        {
            return Res::error('Access Denied',null,HttpResponse::HTTP_FORBIDDEN);
        }



        return $next($request);
    }

    public function accessDenied($request,$scopes,$roles) :bool
    {
        // from developer assingn
        $routeScopes     = explode('|',$scopes);
        $routeRoles      = explode('|',$roles);

        // from keycloak
        $tokenData       = $request->all()['token_data'];
        $tokenScope      = explode(' ',$tokenData['scope']) ;
        $tokenRoles      = $tokenData['realm_access']->roles;      
        
        $response = $this->checkExists($routeScopes,$tokenScope) && $this->checkExists($routeRoles,$tokenRoles);
        return ! $response;
    }

    // Check for the presence of specified scope nd role in the route in the token
    public function checkExists($scopeOrRolesInRoute,array $scopeOrRolesInToken):bool
    {
        // the meaning * is global scope or route
        $hasAccess   = in_array('*',$scopeOrRolesInRoute);

        if (! $hasAccess)
        {
            // check definde scope or role in route exists in token data
            foreach ($scopeOrRolesInRoute as $scopeOrRolesItem) 
            {                
                if (in_array($scopeOrRolesItem,$scopeOrRolesInToken))
                {
                    $hasAccess = true;
                }
            }
        }

        return $hasAccess;
    }
}
