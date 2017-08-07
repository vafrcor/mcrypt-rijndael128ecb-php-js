/*
 * Node JS 8.1.3
 */

var mcrypt = require('js-rijndael'); // npm install js-rijndael --save
var base64Js = require('base64-js'); // npm install base64-js --save
var data='NQOxxkqRe7r336rabsltFaIC06ziEtNMzxfJM3X/wMa6FnwGwZ7MFloYtAvwZ521OdndKIYtEbO/iFRHo/igGfNMJ+x0JmCWuXt9YvMOs28b61unMicVmqp3DWq0jXdn3nlCuA47WwbTf760w1ipbA==';
var encrypted_key="ZAqyuuB77cTBY/Z5p0b3qw==";

try{
 	var key = [].slice.call(base64Js.toByteArray(encrypted_key));
	var iv=null;
	var message = [].slice.call(base64Js.toByteArray(data));
	var clearText = String.fromCharCode.apply(this, mcrypt.decrypt(message, iv, key, 'rijndael-128', 'ecb'));
	console.log('decrypt', clearText);
}catch(err){
 	console.log('error:', err);
}