const express = require('express');
const app = express();

app.get('/', function (req, res) {
  var fs=require('fs');
	var _=require('lodash');
	var php=require('locutus/php');
	var mcrypt = require('js-rijndael');
	var base64 = require('base64-js'); // base64 with byte-array support
	let base64Encryption=require('./libs/base64.js'); // base64 encoding/decoding

	var mcryptDecrypt= function(data){
		let r={result: 'false', message: ''};
		try{
			let plainKey= base64Encryption.decode(data.encrypted_key);
			// let useKey=data.use_key=='plain'? plainKey : data.encrypted_key;
			let useKey=data.encrypted_key;
			r.source= data.source;
			r.key=plainKey;
			r.encrypted_key=data.encrypted_key;
			r.use_key=data.use_key;
			r.encrypted=data.encrypted;
			let key = [].slice.call(base64.toByteArray(useKey));
			let iv=null;
			let message = [].slice.call(base64.toByteArray(data.encrypted));
			// r.message_byte_array=message;
			let clearText = String.fromCharCode.apply(this, mcrypt.decrypt(message, iv, key, 'rijndael-128', 'ecb'));
			r.decrypt=clearText;
			if(data.source=='php'){
				let replaced=['\\u0000','\\u0001','\\u0002','\\u0003','\\u0004','\\u000e'];
				_.forEach(replaced, function(rep){
					let regex = new RegExp(rep, "g");
					r.decrypt=r.decrypt.replace(regex,'');
				});
			}

			let isJson=false;
			try {
				JSON.parse(r.decrypt);
				isJson=true
			} catch (e) {}

			if(isJson){
				r.decrypt_object= JSON.parse(r.decrypt);
			}
			r.result='true';
		}catch(err){
			r.message=err.message;
			console.log('decrypt-error', err);
		}
		return r;
	};

	var testCases=JSON.parse(fs.readFileSync('./test-decrypt-rijndael-128-ecb.json'));

	var jsonData=[];
	_.forEach(testCases, function(testCase){
		jsonData.push(mcryptDecrypt(testCase));
	});
	res.json(jsonData);
})

app.listen(3000, function () {
  console.log('Example app listening on port 3000!');
})