## TwitterOAuth ##
PHP library to communicate with Twitter OAuth API version 1.1.

[![Latest Stable Version](https://poser.pugx.org/ricardoper/twitteroauth/v/stable.svg)](https://packagist.org/packages/ricardoper/twitteroauth) [![Total Downloads](https://poser.pugx.org/ricardoper/twitteroauth/downloads.svg)](https://packagist.org/packages/ricardoper/twitteroauth) [![SensioLabsInsight](https://insight.sensiolabs.com/projects/037e7000-eca4-43a3-b1fd-1f9de8ad310c/mini.png)](https://insight.sensiolabs.com/projects/037e7000-eca4-43a3-b1fd-1f9de8ad310c) ![License](https://poser.pugx.org/ricardoper/twitteroauth/license.svg)

- Namespaced
- PHP 5.3
- [PSR-2](http://www.php-fig.org/psr/psr-2/ "PHP Framework Interop Group")
- [PSR-4](http://www.php-fig.org/psr/psr-4/ "PHP Framework Interop Group")
- OOP<br/>
<br/>

## OAuth Methods Supported ##
- [Single-User OAuth](https://dev.twitter.com/oauth/overview/single-user "Single-user OAuth with Examples")
- [Application-Only Authentication](https://dev.twitter.com/oauth/application-only "Application-only authentication Overview")

**NOTE:** Call media/upload supported, call account/update_profile_background_image not supported.<br/>
<br/>

## Requirements ##
- PHP Version >= 5.3
- PHP cURL extension
- PHP JSON extension
- PHP OpenSSL extension
- Lib cURL

**NOTE:** No external dependencies (Guzzle, Symfony Components. etc...)<br/>
<br/>

## Installation ##
The recommended way to install TwitterOAuth is through [Composer](http://getcomposer.org/):

```json
{
    "require": {
        "ricardoper/twitteroauth": "2.*"
    }
}
```

**NOTE:** If you prefer v1 (One Single File), you can get it in [v1 branch](https://github.com/ricardoper/TwitterOAuth/tree/v1).<br/>
<br/>

## Examples ##
Please, see the examples source code from "Examples" folder.<br/>
<br/>

## Benchmarks ##
Very simple benchmarks from "Examples" source code.

#### Memory Usage ####
**Less than 524Kb** except for image uploading. In this case memory depends on the image size.

#### Stress Bench ####
**Stress bench done without connection request time.** Single run bench done with and without connection request time. Connection request time may vary depending on the internet connection.

**Machine:**<br/>
Intel Core 2 Quad Q6600 2.40GHz<br/>
4Gb RAM<br/>
7200 rpm HDD

**Versions:**<br/>
nginx 1.6.2<br/>
PHP v5.5.9<br/>
Zend OPcache enabled<br/>
SIEGE 3.0.5

**Bench Details:**<br/>
20 concurrent connections<br/>
1000 requests per thread<br/>
No delays between requests<br/>
Command: siege -c20 -b -r1000 "URL"<br/>
<br/>

| Test Name | Execution Time | Requests per Second |
| --- | :----: | :---: |
| v1 ApplicationOnly | 11.44 | 1748.25 |
| v1 SingleUser | 10.05 | 1990.05 |
| v2 ApplicationOnly | 16.62 | 1203.37 |
| v2 SingleUser | 15.61 | 1281.23 |
| v2 ApplicationOnly (Without Composer) | 15.78 | 1267.43 |
| v2 SingleUser (Without Composer) | 15.60 | 1282.05 |
<br/>

| Test Name (Single Run) | Without Req. Time | With Req. Time |
| --- | :----: | :---: |
| v1 ApplicationOnly | 0.003817 | 2.056922 |
| v1 SingleUser | 0.003674 | 1.115811 |
| v2 ApplicationOnly | 0.005201 | 1.553395 |
| v2 SingleUser | 0.005202 | 0.847195 |
| v2 ApplicationOnly (Without Composer) | 0.004513 | 1.547005 |
| v2 SingleUser (Without Composer) | 0.004403 | 0.838964 |
<br/>

**v1 App Only**<br/>
Transactions: 20000 hits<br/>
Availability: 100.00 %<br/>
Elapsed time: 11.44 secs<br/>
Data transferred: 0.46 MB<br/>
Response time:0.01 secs<br/>
Transaction rate: 1748.25 trans/sec<br/>
Throughput:0.04 MB/sec<br/>
Concurrency: 19.91<br/>
Successful transactions: 20000<br/>
Failed transactions: 0<br/>
Longest transaction: 0.03<br/>
Shortest transaction: 0.00

**v1 Single User**<br/>
Transactions: 20000 hits<br/>
Availability: 100.00 %<br/>
Elapsed time: 10.05 secs<br/>
Data transferred: 0.46 MB<br/>
Response time:0.01 secs<br/>
Transaction rate: 1990.05 trans/sec<br/>
Throughput:0.05 MB/sec<br/>
Concurrency: 19.86<br/>
Successful transactions: 20000<br/>
Failed transactions: 0<br/>
Longest transaction: 0.05<br/>
Shortest transaction: 0.00

**v2 App only**<br/>
Transactions: 20000 hits<br/>
Availability: 100.00 %<br/>
Elapsed time: 16.62 secs<br/>
Data transferred: 0.46 MB<br/>
Response time:0.02 secs<br/>
Transaction rate: 1203.37 trans/sec<br/>
Throughput:0.03 MB/sec<br/>
Concurrency: 19.91<br/>
Successful transactions: 20000<br/>
Failed transactions: 0<br/>
Longest transaction: 0.06<br/>
Shortest transaction: 0.00

**v2 Single User**<br/>
Transactions: 20000 hits<br/>
Availability: 100.00 %<br/>
Elapsed time: 15.61 secs<br/>
Data transferred: 0.46 MB<br/>
Response time:0.02 secs<br/>
Transaction rate: 1281.23 trans/sec<br/>
Throughput:0.03 MB/sec<br/>
Concurrency: 19.91<br/>
Successful transactions: 20000<br/>
Failed transactions: 0<br/>
Longest transaction: 0.04<br/>
Shortest transaction: 0.01

**v2 App Only (Without Composer)**<br/>
Transactions: 20000 hits<br/>
Availability: 100.00 %<br/>
Elapsed time: 15.78 secs<br/>
Data transferred: 0.46 MB<br/>
Response time:0.02 secs<br/>
Transaction rate: 1267.43 trans/sec<br/>
Throughput:0.03 MB/sec<br/>
Concurrency: 19.91<br/>
Successful transactions: 20000<br/>
Failed transactions: 0<br/>
Longest transaction: 0.04<br/>
Shortest transaction: 0.00

**v2 Single User (Without Composer)**<br/>
Transactions: 20000 hits<br/>
Availability: 100.00 %<br/>
Elapsed time: 15.60 secs<br/>
Data transferred: 0.46 MB<br/>
Response time:0.02 secs<br/>
Transaction rate: 1282.05 trans/sec<br/>
Throughput:0.03 MB/sec<br/>
Concurrency: 19.90<br/>
Successful transactions: 20000<br/>
Failed transactions: 0<br/>
Longest transaction: 0.06<br/>
Shortest transaction: 0.00<br/>
<br/>

## License ##
Released under the MIT License.
