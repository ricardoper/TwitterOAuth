## TwitterOAuth ##
PHP library to communicate with Twitter OAuth API version 1.1.

[![Latest Stable Version](https://poser.pugx.org/ricardoper/twitteroauth/v/stable.svg)](https://packagist.org/packages/ricardoper/twitteroauth) [![Total Downloads](https://poser.pugx.org/ricardoper/twitteroauth/downloads.svg)](https://packagist.org/packages/ricardoper/twitteroauth) [![SensioLabsInsight](https://insight.sensiolabs.com/projects/037e7000-eca4-43a3-b1fd-1f9de8ad310c/mini.png)](https://insight.sensiolabs.com/projects/037e7000-eca4-43a3-b1fd-1f9de8ad310c) ![License](https://poser.pugx.org/ricardoper/twitteroauth/license.svg)

- Namespaced
- PHP 5.3
- [PSR-2](http://www.php-fig.org/psr/psr-2/ "PHP Framework Interop Group")
- [PSR-4](http://www.php-fig.org/psr/psr-4/ "PHP Framework Interop Group")
- OOP


## OAuth Methods Supported ##
- [Single-User OAuth](https://dev.twitter.com/oauth/overview/single-user "Single-user OAuth with Examples")
- [Application-Only Authentication](https://dev.twitter.com/oauth/application-only "Application-only authentication Overview")

**NOTE:** Call media/upload supported, call account/update_profile_background_image not supported.


## Requirements ##
- PHP Version >= 5.3
- PHP cURL extension
- PHP JSON extension
- PHP OpenSSL extension
- Lib cURL

**NOTE:** No external dependencies (Guzzle, Symfony Components. etc...)


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

### Memory Usage ###
**Less than 524Kb** except for image uploading. In this case memory depends on the image size.

### Stress Bench ###
Bench done **without connection times**. Connection times may vary depending on the internet connection.

**Machine:**
Intel Core 2 Quad Q6600 2.40GHz
4Gb RAM
7200 rpm HDD

**Versions:**
nginx 1.6.2
PHP v5.5.9
Zend OPcache enabled
SIEGE 3.0.5

**Bench Details:**
20 concurrent connections
1000 requests per thread
No delays between requests
Command: siege -c20 -b -r1000 "URL"

| Test Name | Execution Time | Requests per Second |
| --- | :----: | :---: |
| v1 ApplicationOnly | 11.44 | 1748.25 |
| v1 SingleUser | 10.05 | 1990.05 |
| v2 ApplicationOnly | 16.62 | 1203.37 |
| v2 SingleUser | 15.61 | 1281.23 |
| v2 ApplicationOnly (Without Composer) | 15.78 | 1267.43 |
| v2 SingleUser (Without Composer) | 15.60 | 1282.05 |

#### In Detail: ####
**v1 App Only**
Transactions: 20000 hits
Availability: 100.00 %
Elapsed time: 11.44 secs
Data transferred: 0.46 MB
Response time:  0.01 secs
Transaction rate: 1748.25 trans/sec
Throughput:  0.04 MB/sec
Concurrency: 19.91
Successful transactions: 20000
Failed transactions: 0
Longest transaction: 0.03
Shortest transaction: 0.00

**v1 Single User**
Transactions: 20000 hits
Availability: 100.00 %
Elapsed time: 10.05 secs
Data transferred: 0.46 MB
Response time:  0.01 secs
Transaction rate: 1990.05 trans/sec
Throughput:  0.05 MB/sec
Concurrency: 19.86
Successful transactions: 20000
Failed transactions: 0
Longest transaction: 0.05
Shortest transaction: 0.00

**v2 App only**
Transactions: 20000 hits
Availability: 100.00 %
Elapsed time: 16.62 secs
Data transferred: 0.46 MB
Response time:  0.02 secs
Transaction rate: 1203.37 trans/sec
Throughput:  0.03 MB/sec
Concurrency: 19.91
Successful transactions: 20000
Failed transactions: 0
Longest transaction: 0.06
Shortest transaction: 0.00

**v2 Single User**
Transactions: 20000 hits
Availability: 100.00 %
Elapsed time: 15.61 secs
Data transferred: 0.46 MB
Response time:  0.02 secs
Transaction rate: 1281.23 trans/sec
Throughput:  0.03 MB/sec
Concurrency: 19.91
Successful transactions: 20000
Failed transactions: 0
Longest transaction: 0.04
Shortest transaction: 0.01

**v2 App Only (Without Composer)**
Transactions: 20000 hits
Availability: 100.00 %
Elapsed time: 15.78 secs
Data transferred: 0.46 MB
Response time:  0.02 secs
Transaction rate: 1267.43 trans/sec
Throughput:  0.03 MB/sec
Concurrency: 19.91
Successful transactions: 20000
Failed transactions: 0
Longest transaction: 0.04
Shortest transaction: 0.00

**v2 Single User (Without Composer)**
Transactions: 20000 hits
Availability: 100.00 %
Elapsed time: 15.60 secs
Data transferred: 0.46 MB
Response time:  0.02 secs
Transaction rate: 1282.05 trans/sec
Throughput:  0.03 MB/sec
Concurrency: 19.90
Successful transactions: 20000
Failed transactions: 0
Longest transaction: 0.06
Shortest transaction: 0.00


## License ##
Released under the MIT License.
