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

            $explodeToken = explode('.',$token);
            $tokenData    = base64_decode($explodeToken[1]);
            $tokenData    = (array)json_decode($tokenData);
            if (config('keycloak-middleware.public_key')) 
            {
                $tokenData = JWT::decode($token, new Key($publicKey, 'RS256'));
            }

            request()->merge([
                'token_data'=> $tokenData ,
                'uuid' => is_array($tokenData) ? $tokenData['sub'] : $tokenData->sub
            ]);
        } 
        catch (Exception $e) 
        {
            $errorCode = $e->getCode();
            $fail("the :attribute is not valid");
        }      
    }
}
