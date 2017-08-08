# mcrypt-rijndael128ecb-php-js

Rijndael-128-EBC encryption and decryption compatibility between PHP and NodeJS (Javascript).

##PHP
- version `< 7.1.x`, using PHP default Mcrypt encrypt + decrypt.
- version `>= 7.1.x`, using [PHP Seclib Mycrypt](https://github.com/phpseclib/mcrypt_compat).

##NodeJS
- version `6.x.x > x > 8.1.3` 
- using [JS-Rijndael](https://www.npmjs.com/package/js-rijndael)
- dependencies:
-- [lodash](https://www.npmjs.com/package/lodash)
-- [base64-js](https://www.npmjs.com/package/base64-js)
-- Base64 encoder/decoder.