<?php 
namespace Pickmap\Keycloak;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Exception;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class TokenValidator implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        try {
            $token     = $value;
            $publicKeyConfig = config('keycloak-middleware.public_key'); 
      
            $publicKey = <<<EOD
            -----BEGIN PUBLIC KEY-----
            $publicKeyConfig
            -----END PUBLIC KEY-----
            EOD;            
            $decoded = JWT::decode($token, new Key($publicKey, 'RS256'));
            request()->merge(['token_data'=> (array)$decoded ,'uuid' => $decoded->sub]);
        } 
        catch (Exception $e) 
        {
            $errorCode = $e->getCode();
            $fail("the :attribute is not valid");
        }      
    }
}