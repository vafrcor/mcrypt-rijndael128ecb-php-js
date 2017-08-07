/*
 * Node JS 8.1.3
 */

var mcrypt = require('js-rijndael'); // npm install js-rijndael --save
var base64Js = require('base64-js'); // npm install base64-js --save
var data='gVd37sq7h/d4l2Q1njoQ2ry/V9sO43P17H/1P+/2BT68qXk3nz8E3ckMdDCJ/3jVMCAqFx3jbzOIluJdXmHHZQzInWXHfYepKoQyKTQ9eWCCgAsdqZuud78kHXrt0jdPT18Q54ffVvH/OYZ+nRkBNA==';
var encrypted_key="MDEyMzQ1Njc4OWFiY2RlZg==";

try{
 	var key = [].slice.call(base64Js.toByteArray(encrypted_key));
	var iv=null;
	var message = [].slice.call(base64Js.toByteArray(data));
	var clearText = String.fromCharCode.apply(this, mcrypt.decrypt(message, iv, key, 'rijndael-128', 'ecb'));
	console.log('decrypt', clearText);
}catch(err){
 	console.log('error:', err);
}