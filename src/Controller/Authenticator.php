<?php

class Authenticator
{
    public function authenticate()
    {
        switch(true) {
            case array_key_exists('HTTP_AUTHORIZATION', $_SERVER) :
                $authHeader = $_SERVER['HTTP_AUTHORIZATION'];
                break;
            case array_key_exists('Authorization', $_SERVER) :
                $authHeader = $_SERVER['Authorization'];
                break;
            default :
                $authHeader = null;
                break;
        }
        
        preg_match('/Bearer\s(\S+)/', $authHeader, $matches);
        
        if(!isset($matches[1])) {
            return 'No Bearer Token';
        }
        
        return $this->verify($matches[1]);
    }
    
    private function verifyToken($token)
    {
        return $token === getenv('TOKEN');
    }
}
