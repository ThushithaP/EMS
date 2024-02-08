<?php 
namespace App\Services;

class EncryptionService {

    /**
     * Encrypt the strings
     * @param $plainValue - string to be encrypted
     * retun String
     */
    public static function encrypt($plainValue , $key) {
        if(!$plainValue){ return false; }

        $key = substr(str_pad($key,16,'0'),0,16);
        $encryptedValue = openssl_encrypt($plainValue, CIPHER, $key, 0, getenv('FIXED_IV'));
        return base64_encode($encryptedValue);  
    }

    /**
     * Decrypt the strings
     * @param $encryptedValue - encrypted string
     * retun String
     */
    public static function decrypt($encryptedValue, $key) {
        if(!$encryptedValue){ return false; }
        
        $key = substr(str_pad($key, 16, '0'), 0, 16);
        $decryptedValue = openssl_decrypt(base64_decode($encryptedValue), CIPHER, $key, 0, getenv('FIXED_IV'));
        return $decryptedValue;
    }
}
?>