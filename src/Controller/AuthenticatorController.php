<?php

namespace src\Controller;

class AuthenticatorController 
{
    public function authenticate()
    {
        $headers = apache_request_headers();
        
        switch(true) {
            case array_key_exists('HTTP_AUTHORIZATION', $headers) :
                $authHeader = $headers['HTTP_AUTHORIZATION'];
                break;
            case array_key_exists('Authorization', $headers) :
                $authHeader = $headers['Authorization'];
                break;
            default :
                $authHeader = null;
                break;
        }
        
        preg_match('/Bearer\s(\S+)/', $authHeader, $matches);
        if(!isset($matches[1])) {
            return 'No Bearer Token';
        }
        
        return self::verifyToken($matches[1]);
    }
    
    private function verifyToken($token)
    {
        return $token === env('TOKEN');
    }
}
