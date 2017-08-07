<?php
/**
 * For PHP version < 7.1
 */
function rijndael128_ecb_encrypt($str=null, $plain_key=null)
{
    $block = mcrypt_get_block_size('rijndael_128', 'ecb');
    $pad = $block - (strlen($str) % $block);
    $str .= str_repeat(chr($pad), $pad);
    return base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $plain_key, $str, MCRYPT_MODE_ECB));
}

function rijndael128_ecb_decrypt($str=null, $plain_key=null)
{
    $str = base64_decode($str);
    $str = mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $plain_key, $str, MCRYPT_MODE_ECB);
    $block = mcrypt_get_block_size('rijndael_128', 'ecb');
    $pad = ord($str[($len = strlen($str)) - 1]);
    $len = strlen($str);
    $pad = ord($str[$len-1]);
    return substr($str, 0, strlen($str) - $pad);
}

/**
 * For PHP version >= 7.1
 */
function openssl_rijndael128_ecb_encrypt($str=null, $plain_key=null)
{
    return openssl_encrypt($str, 'aes-128-ecb', $plain_key, 0);
}

function openssl_rijndael128_ecb_decrypt($str=null, $plain_key=null)
{
    return openssl_decrypt($str, 'aes-128-ecb', $plain_key, 0);
}

/**
 * Example Test compatibility
 */

$enc_key='MDEyMzQ1Njc4OWFiY2RlZg==';
$key=base64_decode($enc_key);
$json_data='{"session":"test1","offerID":"test","voucherCode":"YuEAsw","phoneNo":"081234000001","timestamp":"1495538522"}';
$openssl_encrypt=openssl_rijndael128_ecb_encrypt($json_data, $key);
$openssl_decrypt=openssl_rijndael128_ecb_decrypt($openssl_encrypt, $key);
$mcrypt_decrypt=rijndael128_ecb_decrypt($openssl_encrypt, $key);
$debug=[
	'key'=>$key,
	'encrypted_key'=>$enc_key,
	'data'=>$json_data,
	'openssl_encrypt'=>$openssl_encrypt,
	'openssl_decrypt'=>$openssl_decrypt,
	'mcrypt_decrypt'=>$mcrypt_decrypt
];
echo '<pre>';
print_r($debug);
echo '</pre>';
