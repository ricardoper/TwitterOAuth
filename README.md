## TwitterOAuth ##

PHP library to communicate with Twitter OAuth API version 1.1.

[![Latest Stable Version](https://poser.pugx.org/ricardoper/twitteroauth/v/stable.svg)](https://packagist.org/packages/ricardoper/twitteroauth) [![Total Downloads](https://poser.pugx.org/ricardoper/twitteroauth/downloads.svg)](https://packagist.org/packages/ricardoper/twitteroauth) [![Latest Unstable Version](https://poser.pugx.org/ricardoper/twitteroauth/v/unstable.svg)](https://packagist.org/packages/ricardoper/twitteroauth) [![License](https://poser.pugx.org/ricardoper/twitteroauth/license.svg)](https://packagist.org/packages/ricardoper/twitteroauth)

- Namespaced
- PHP 5.3
- [PSR-2](http://www.php-fig.org/psr/psr-2/ "PHP Framework Interop Group")
- [PSR-4](http://www.php-fig.org/psr/psr-4/ "PHP Framework Interop Group")
- OOP

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
        "ricardoper/twitteroauth": "2.*@dev"
    }
}
```

## Example ##



## Benchmarks v2 ##

Very simple benchmarks from "Examples" source code.

- Connection time excluded
- Twitter API response time excluded
- Browser render time included
- Nginx v1.6.2
- PHP 5.5.9
- Xdebug v2.2.3 Loaded
- Zend OPcache Loaded

#### Memory Usage ####
Less than 524Kb except for image uploading. In this case memory depends on the image size.

#### Load Times ####

![ApplicationOnly_Minimal](https://raw.githubusercontent.com/ricardoper/TwitterOAuth/v2/Docs/Benchs/Time_ApplicationOnly_Minimal.png)

![ApplicationOnly_BearerToken](https://raw.githubusercontent.com/ricardoper/TwitterOAuth/v2/Docs/Benchs/Time_ApplicationOnly_BearerToken.png)

![SingleUser](https://raw.githubusercontent.com/ricardoper/TwitterOAuth/v2/Docs/Benchs/Time_SingleUser.png)

![SingleUser_UploadingMedia](https://raw.githubusercontent.com/ricardoper/TwitterOAuth/v2/Docs/Benchs/Time_SingleUser_UploadingMedia.png)


## Profiling v2 ##

Profiling grind from "Examples" source code.

- Connection time excluded
- Twitter API response time excludedd
- Nginx v1.6.2
- PHP 5.5.9
- Xdebug v2.2.3 Loaded
- Zend OPcache Loaded

![ApplicationOnly_Minimal](https://raw.githubusercontent.com/ricardoper/TwitterOAuth/v2/Docs/Benchs/Prof_ApplicationOnly_Minimal.png)

![ApplicationOnly_BearerToken](https://raw.githubusercontent.com/ricardoper/TwitterOAuth/v2/Docs/Benchs/Prof_ApplicationOnly_BearerToken.png)

![SingleUser](https://raw.githubusercontent.com/ricardoper/TwitterOAuth/v2/Docs/Benchs/Prof_SingleUser.png)

![SingleUser_UploadingMedia](https://raw.githubusercontent.com/ricardoper/TwitterOAuth/v2/Docs/Benchs/Prof_SingleUser_UploadingMedia.png)



## License ##

Released under the MIT License.
