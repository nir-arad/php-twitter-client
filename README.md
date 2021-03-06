# php-twitter-client: A Twitter Client library in PHP

[![Software License][ico-license]](LICENSE.md)
![GitHub Release Downloads][ico-github-downloads]
[![Latest Version on Packagist][ico-version]][link-packagist]
![Packagist stars][ico-packagist-stars]

![GitHub Code Size][ico-github-code-size]
[![GitHub Workflow Build Status][ico-github-workflow]][link-github-workflow]
[![GitHub open issues][ico-github-issues-open]][link-github-issues-open]
[![GitHub closed issues][ico-github-issues-closed]][link-github-issues-closed]



## Structure

If any of the following are applicable to your project, then the directory structure should follow industry best practices by being named the following.

```
bin/        
build/
docs/
config/
src/
tests/
vendor/
```

---
## Install

Via Composer

``` bash
$ composer require nir-arad/php-twitter-client
```

---
## Test

``` bash
$ composer test
```

---
## Usage

Below is a usage example to fetch a tweet and display its contents and attributes.

---
### Step 1. Sign up as a developer

[Twitter Developer Home](https://developer.twitter.com/)
---
### Step 2. Create a project
---
### Step 3. Get project credentials

You will need to obtain both {api_key, api_secret} tokens for Oauth v1 based APIs, and a bearer token for Oauth v2 based APIs.

Store the credentials in a file (e.g. "project.json"). The file format is as follows:

``` json
{
    "bearer_token": "AAAAAAAAAAAAAAAAAAAA...",
    "api_key": "4FlE...",
    "api_secret": "OewZ..."
}
```
---
### Step 4. Obtain user credentials

Store the credentials in a file (e.g. "user.json"). The file format is as follows:
``` json
{
    "oauth_token": "123...",
    "oauth_token_secret": "wWby...",
    "user_id": "987...",
    "screen_name": "MyTwitterUser"
}
```
---
### Step 5. Code example


``` php
use NirArad\TwitterClient;

$p_cred = new TwitterClient\ProjectCredentials();
$p_cred->from_file($p_cred_file);

$u_cred = new TwitterClient\UserCredentials();
$u_cred->from_file($u_cred_file);

$client = new TwitterClient\TwitterClient();
$client->project_credentials = $p_cred;
$client->user_credentials = $u_cred;

$params = new TwitterClient\v1\Tweets\GetStatusesLookupQueryParams();
$params_array = array(
    "id" => array(1326023218772144134)
);
$params->from_array($params_array)
$response = $client->GetStatusesLookup($params);

var_dump($response);

```

## Change log

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) and [CODE_OF_CONDUCT](CODE_OF_CONDUCT.md) for details.

## Security

If you discover any security related issues, please email narad1972@gmail.com instead of using the issue tracker.

## Credits

- [Nir Arad][link-author]
- [All Contributors][link-contributors]

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

<!-- License -->
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square

<!-- Packagist -->
[ico-version]: https://img.shields.io/packagist/v/nir-arad/php-twitter-client.svg?style=flat-square
[link-packagist]: https://packagist.org/packages/nir-arad/php-twitter-client

[ico-packagist-stars]: https://img.shields.io/packagist/stars/nir-arad/php-twitter-client

<!-- GitHub Release Downloads -->
[ico-github-downloads]: https://img.shields.io/github/downloads/nir-arad/php-twitter-client/total

<!-- GitHub Code Size -->
[ico-github-code-size]: https://img.shields.io/github/languages/code-size/nir-arad/php-twitter-client

<!-- GitHub Workflow Build Status -->
[ico-github-workflow]: https://img.shields.io/github/workflow/status/nir-arad/php-twitter-client/PHP%20Composer/master
[link-github-workflow]: https://github.com/nir-arad/php-twitter-client/actions?query=workflow%3A%22PHP+Composer%22

[ico-github-issues-open]: https://img.shields.io/github/issues/nir-arad/php-twitter-client
[link-github-issues-open]: https://github.com/nir-arad/php-twitter-client/issues?q=is%3Aopen

[ico-github-issues-closed]: https://img.shields.io/github/issues-closed/nir-arad/php-twitter-client
[link-github-issues-closed]: https://github.com/nir-arad/php-twitter-client/issues?q=is%3Aissue+is%3Aclosed
