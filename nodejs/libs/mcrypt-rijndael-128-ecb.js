'use strict';

//# check required modules
var module_exists = require('module-exists');
var required_modules=['js-rijndael', 'base64-js', 'lodash'];
var valid_requirement=required_modules.length;
var not_exist_module=[];
required_modules.forEach(function(em){
	if(module_exists(em) === false){
		valid_requirement--;
		not_exist_module.push(em);
	}
});

if(valid_requirement != required_modules.length){
	console.error('required module not exist: '+not_exist_module.join(','));
	process.exit(1);
}

var _=require('lodash');
var mcrypt = require('js-rijndael');
var base64 = require('base64-js'); // support byte support
// var base64Encryption=require('./base64.js'); 
// base64 encoding/decoding

var exports={
	debugMode: true,
	encrypt: function(plainStr, encryptedKey){
		let self=this;
		let debug={};
		var encryptedBs64=null;
		try{
			let key = [].slice.call(base64.toByteArray(encryptedKey));
			let iv=null;
			let message_bytes=plainStr.split('').map(function(c){ 
				return c.charCodeAt(); 
			});
			let message =[].slice.call(message_bytes);	
			let encryptedByteArray = mcrypt.encrypt(message, iv, key, 'rijndael-128', 'ecb');
			encryptedBs64=base64.fromByteArray(encryptedByteArray);
			if(self.debugMode==true){
				debug.plain_string=plainStr;
				// debug.message_bytes=message_bytes;
				// debug.message=message;
				// debug.ct_btye_array=encryptedByteArray;
				debug.ct_b64=encryptedBs64;
				if(self.debugMode==true){
					console.log('Mcrypt\\Encrypt\\Debug: ', debug);
				}
			}
		}catch(err){
			if(self.debugMode==true){
				console.log('Mcrypt\\Encrypt\\Failed\\Reason: ',err);
			}
		}
		return encryptedBs64;
	},
	decrypt: function(chiperTextBase64, encryptedKey){
		let self=this;
		let debug={};
		var clearText=null;
		try{
			let key = [].slice.call(base64.toByteArray(encryptedKey));
			let iv=null;
			let message = [].slice.call(base64.toByteArray(chiperTextBase64));
			clearText = String.fromCharCode.apply(this, mcrypt.decrypt(message, iv, key, 'rijndael-128', 'ecb'));

			//# For encrypted string that generated from PHP, we need to clean some padding characters.
			if(_.isEmpty(clearText) === false){
				let paddingReplaced=['\\u0000','\\u0001','\\u0002','\\u0003','\\u0004','\\u000e'];
				paddingReplaced.forEach(function(rep){
					let regex = new RegExp(rep, "g");
					clearText=clearText.replace(regex,'');
				});
			}

			if(self.debugMode==true){
				debug.chiper_text=chiperTextBase64;
				// debug.message_byte_array=message;
				debug.original_text=clearText;
				if(self.debugMode==true){
					console.log('Mcrypt\\Decrypt\\Debug: ', debug);
				}
			}
		}catch(err){
			if(self.debugMode==true){
				console.log('Mcrypt\\Decrypt\\Failed\\Reason: ',err);
			}
		}
		return clearText;
	}
};

module.exports= exports;