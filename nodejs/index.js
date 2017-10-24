const express = require('express');
const app = express();

app.get('/', function(req, res){

	res.set({
	  'Content-Type': 'text/html'
	});
	res.send('<!DOCTYPE html><html><body><ul>'+
		'<li><a href="/mass-decrypt" title="Decrypt multiple chiper-text">Mass Decrypt</a></li>'+
		'<li><a href="/encrypt" title="Encrypt test">Encrypt</a></li>'+
		'<li><a href="/decrypt" title="Decrypt test">Decrypt</a></li>'+
		'</ul></body></html>');
});

app.get('/encrypt', function (req, res) {
	var _=require('lodash');
	var mcryptRjndael128ECB=require('./libs/mcrypt-rijndael-128-ecb.js');
	var base64Encryption=require('./libs/base64.js'); // base64 encoding/decoding
	var base64 = require('base64-js'); // base64 with byte-array support

	var d={
		key: 'abcdefghij012345',
		encrypted_key: base64Encryption.encode('abcdefghij012345'),
		plain_text: 'Hello World!'
	};
	d.chiper_text=mcryptRjndael128ECB.encrypt(d.plain_text, d.encrypted_key);
	res.json(d);
});

app.get('/decrypt', function (req, res) {
	var _=require('lodash');
	var mcryptRjndael128ECB=require('./libs/mcrypt-rijndael-128-ecb.js');
	var base64Encryption=require('./libs/base64.js'); // base64 encoding/decoding
	var base64 = require('base64-js'); // base64 with byte-array support

	var d={
		key: 'abcdefghij012345',
		encrypted_key: base64Encryption.encode('abcdefghij012345'),
		chiper_text: 'BYnAr/AYBdOlJHUn/kgyBw=='
	};
	d.plain_text=mcryptRjndael128ECB.decrypt(d.chiper_text, d.encrypted_key);
	res.json(d);
});

app.get('/mass-decrypt', function (req, res) {
  	var fs=require('fs');
	var _=require('lodash');
	var mcryptRjndael128ECB=require('./libs/mcrypt-rijndael-128-ecb.js');
	var base64Encryption=require('./libs/base64.js'); // base64 encoding/decoding
	var base64 = require('base64-js'); // base64 with byte-array support
	
	var mcryptDecrypt= function(data){
		let r={result: 'false', message: ''};
		try{
			let plainKey= base64Encryption.decode(data.encrypted_key);
			let useKey=data.encrypted_key;
			r.source= data.source;
			r.key=plainKey;
			r.encrypted_key=data.encrypted_key;
			r.encrypted=data.encrypted;
			r.decrypt=mcryptRjndael128ECB.decrypt(data.encrypted, data.encrypted_key);
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