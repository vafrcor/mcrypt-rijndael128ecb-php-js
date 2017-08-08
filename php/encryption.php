<?php

function cryptoJsAesEncrypt($passphrase, $value){
    $salt = openssl_random_pseudo_bytes(8);
    $salted = '';
    $dx = '';
    while (strlen($salted) < 48) {
        $dx = md5($dx.$passphrase.$salt, true);
        $salted .= $dx;
    }
    $key = substr($salted, 0, 32);
    $iv  = substr($salted, 32,16);
    $encrypted_data = openssl_encrypt(json_encode($value), 'aes-256-cbc', $key, true, $iv);
    $data = array("ct" => base64_encode($encrypted_data), "iv" => bin2hex($iv), "s" => bin2hex($salt));
    return json_encode($data);
}

function cryptoJsAesDecrypt($passphrase, $jsonString){
    $jsondata = json_decode($jsonString, true);
    $salt = hex2bin($jsondata["s"]);
    $ct = base64_decode($jsondata["ct"]);
    $iv  = hex2bin($jsondata["iv"]);
    $concatedPassphrase = $passphrase.$salt;
    $md5 = array();
    $md5[0] = md5($concatedPassphrase, true);
    $result = $md5[0];
    for ($i = 1; $i < 3; $i++) {
        $md5[$i] = md5($md5[$i - 1].$concatedPassphrase, true);
        $result .= $md5[$i];
    }
    $key = substr($result, 0, 32);
    $data = openssl_decrypt($ct, 'aes-256-cbc', $key, true, $iv);
    return json_decode($data, true);
}

function rijndael128_ecb_encrypt($str, $key)
{   
    $block = mcrypt_get_block_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_ECB);
    $pad = $block - (strlen($str) % $block);
    $str .= str_repeat(chr($pad), $pad);
    return base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $key, $str, MCRYPT_MODE_ECB));
}

function rijndael128_ecb_decrypt($str, $key)
{   
    // $iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_ECB);
    // $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
    $str = base64_decode($str);
    $str = mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $key, $str, MCRYPT_MODE_ECB);
    $block = mcrypt_get_block_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_ECB);
    $pad = ord($str[($len = strlen($str)) - 1]);
    $len = strlen($str);
    $pad = ord($str[$len-1]);
    return substr($str, 0, strlen($str) - $pad);
}

function seclib_rijndael128_ecb_encrypt($str, $key)
{   
    $iv_size = phpseclib_mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_ECB);
    $iv = phpseclib_mcrypt_create_iv($iv_size, MCRYPT_RAND);

    $block = phpseclib_mcrypt_get_block_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_ECB);
    $pad = $block - (strlen($str) % $block);
    $str .= str_repeat(chr($pad), $pad);
    return base64_encode(phpseclib_mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $key, $str, MCRYPT_MODE_ECB, $iv));
}

function seclib_rijndael128_ecb_decrypt($str, $key)
{
    $iv_size = phpseclib_mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_ECB);
    $iv = phpseclib_mcrypt_create_iv($iv_size, MCRYPT_RAND);

    $str = base64_decode($str);
    $str = phpseclib_mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $key, $str, MCRYPT_MODE_ECB, $iv);
    $block = phpseclib_mcrypt_get_block_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_ECB);
    $pad = ord($str[($len = strlen($str)) - 1]);
    $len = strlen($str);
    $pad = ord($str[$len-1]);
    return substr($str, 0, strlen($str) - $pad);
}

function openssl_rijndael128_ecb_encrypt($str=null, $key=null)
{
    return openssl_encrypt($str, 'aes-128-ecb', $key, 0);
}

function openssl_rijndael128_ecb_decrypt($str=null, $key=null)
{
    return openssl_decrypt(rtrim($str), 'aes-128-ecb', $key, 0);
}

function is_json($str=null)
{
    $r= json_decode($str);
    if(($r === false) || empty($r)){
        return false;
    }
    return true;
}

function is_ascii($str) {
    $r=true;
    $len = strlen($str);
    for ($i = 0; $i < $len; $i++) {
        if (ord($str[$i]) > 127){
            $r= false;
            break;
        }
    }
    return $r;
}

function is_utf8($string) {
  return (mb_detect_encoding($string, 'UTF-8', true) == 'UTF-8');
}

function utf8($utf8){   
    if(mb_detect_encoding($utf8,'UTF-8',true) =='UTF-8'){
        return $utf8;
    }else {
        $utf8=iconv("windows-1256","utf-8",$utf8);
        return $utf8;
    }
}

function hex2str($hex) {
    $str = '';
    for($i=0;$i<strlen($hex);$i+=2) $str .= chr(hexdec(substr($hex,$i,2)));
    return $str;
}