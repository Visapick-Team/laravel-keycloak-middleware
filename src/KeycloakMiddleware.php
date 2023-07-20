<?php

namespace Alireza\Keycloak;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response as HttpResponse;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class KeycloakMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
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
            return Response()->json(
                [
                'has_error' => true,
                'errors' => $validator->errors()
                ] , HttpResponse::HTTP_UNAUTHORIZED
            );
        }
        return $next($request);
    }
}