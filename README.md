## TwitterOAuth ##
PHP library to communicate with Twitter OAuth API version 1.1.

[![Latest Stable Version](https://poser.pugx.org/ricardoper/twitteroauth/v/stable.svg)](https://packagist.org/packages/ricardoper/twitteroauth) [![Total Downloads](https://poser.pugx.org/ricardoper/twitteroauth/downloads.svg)](https://packagist.org/packages/ricardoper/twitteroauth) [![Latest Unstable Version](https://poser.pugx.org/ricardoper/twitteroauth/v/unstable.svg)](https://packagist.org/packages/ricardoper/twitteroauth) [![License](https://poser.pugx.org/ricardoper/twitteroauth/license.svg)](https://packagist.org/packages/ricardoper/twitteroauth)

- Namespaced
- PHP 5.3
- [PSR-2](http://www.php-fig.org/psr/psr-2/ "PHP Framework Interop Group")
- [PSR-4](http://www.php-fig.org/psr/psr-4/ "PHP Framework Interop Group")
- OOP


## OAuth Methods Supported ##
- [Single-User OAuth](https://dev.twitter.com/oauth/overview/single-user "Single-user OAuth with Examples")
- [Application-Only Authentication](https://dev.twitter.com/oauth/application-only "Application-only authentication Overview")


## Requirements ##
- PHP Version >= 5.3
- PHP cURL extension
- PHP JSON extension
- PHP OpenSSL extension
- Lib cURL

## Installation ##
The recommended way to install TwitterOAuth is through [Composer](http://getcomposer.org/):

```json
{
    "require": {
        "ricardoper/twitteroauth": "2.*"
    }
}
```

**NOTE:** If you prefer v1 (One Single File), you can get it in [v1 branch](https://github.com/ricardoper/TwitterOAuth/tree/v1).

## Examples ##
Please, see the examples source code from "Examples" folder.


## Benchmarks ##
Very simple benchmarks from "Examples" source code.

#### Memory Usage ####
Less than 524Kb except for image uploading. In this case memory depends on the image size.


## License ##
Released under the MIT License.
