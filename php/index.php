<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL|E_STRICT);

require __DIR__ .''.DIRECTORY_SEPARATOR.'vendor'.DIRECTORY_SEPARATOR.'autoload.php';
require __DIR__ .''.DIRECTORY_SEPARATOR.'encryption.php';

try{
	$test_cases=json_decode(file_get_contents(__DIR__.''.DIRECTORY_SEPARATOR.'test-decrypt-rijndael-128-ecb.json'), true);
	
	$test_decrypt= function($data=null){
		$r=['result'=>'false','message'=>''];
		try{
			$enc_key=$data['encrypted_key'];
			$key=base64_decode($enc_key);
			
			// $use_key=$key;
			$decrypt_mcrypt= rijndael128_ecb_decrypt($data['encrypted'], $key);
			$decrypt_openssl= openssl_rijndael128_ecb_decrypt($data['encrypted'], $key);
			$decrypt_phpseclib_mcrypt=seclib_rijndael128_ecb_decrypt($data['encrypted'], $key);
			$decrypt_mcrypt_type=gettype($decrypt_mcrypt);
			$decrypt_openssl_type=gettype($decrypt_openssl);
			$decrypt_phpseclib_type=gettype($decrypt_phpseclib_mcrypt);

			if($decrypt_openssl_type == 'string'){
				$decrypt_openssl=trim($decrypt_openssl);
			}

			// $replaced_eot=['\u0000','\u0001','\u0002','\u0003','\u0004'];
			if($data['source']=='javascript'){
				$decrypt_mcrypt=trim($decrypt_mcrypt);
				$decrypt_phpseclib_mcrypt=trim($decrypt_phpseclib_mcrypt);
			}
			
			$r['debug']=[
				'source'=>$data['source'],
				'key_utf8'=>utf8_encode($key), // handle Latin-1 encoding
				'key_mbconvert'=>mb_convert_encoding($key, "UTF-8", "Windows-1252"),
				'key_iconv'=>iconv("CP1252", "UTF-8", $key),
				'encrypted_key'=>$enc_key,
				'use_key'=>'plain',
				'encrypted'=>$data['encrypted'],
				'decrypt_mcrypt'=>$decrypt_mcrypt,
				'decrypt_openssl'=>$decrypt_openssl,
				'decrypt_phpseclib_mcrypt'=>$decrypt_phpseclib_mcrypt
			];
			if($decrypt_openssl_type == 'boolean'){
				$r['result']='false';
				$r['message']='Openssl Decrypt \ '.openssl_error_string();
			}

			if(($decrypt_mcrypt_type == 'string') && is_json($decrypt_mcrypt)){
				$r['debug']['decrypt_mcrypt_json']=json_decode($decrypt_mcrypt, true);
			}

			if(($decrypt_openssl_type == 'string') && is_json($decrypt_openssl)){
				$r['debug']['decrypt_openssl_json']=json_decode($decrypt_openssl, true);
			}
			
			if(($decrypt_phpseclib_type == 'string') && is_json($decrypt_phpseclib_mcrypt)){
				$r['debug']['decrypt_phpseclib_mcrypt_json']=json_decode($decrypt_phpseclib_mcrypt, true);
			}

			$r['result']='true';
		}catch(\Exception $ei){
			$r['message']= $ei->getMessage().' ('.$ei->getLine().' @ '.$ei->getFile().')';
		}
		return $r;
	};

	$json_data=[];
	foreach($test_cases as $case){
		$json_data[]=$test_decrypt($case);
	}
	if(isset($_GET['return']) && $_GET['return']=='json'){
		header('Content-Type: application/json');
		$json= json_encode($json_data, JSON_PRETTY_PRINT);
		// var_dump($json);
		echo $json;
		exit();
	}else{
		echo '<pre>';
		print_r($json_data);
		echo '</pre>';
	}
}catch(\Exception $e){
	echo $e->message().'<br>';
	echo '<pre>';
	print_r($e->getTrace());
	echo '</pre>';
}