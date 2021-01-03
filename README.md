# php-twitter-client

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE.md)
[![Build Status][ico-gitlab-build]][link-gitlab-build]
[![Pipeline status][ico-gitlab-pipeline]][link-gitlab-pipeline]
[![Coverage report][ico-gitlab-cov]][link-gitlab-cov]
[![Total Downloads][ico-downloads]][link-downloads]
<!-- [![Coverage Status][ico-scrutinizer]][link-scrutinizer] -->
<!-- [![Quality Score][ico-code-quality]][link-code-quality] -->

This is where your description should go. Try and limit it to a paragraph or two, and maybe throw in a mention of what PSRs you support to avoid any confusion with users and contributors.

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
use nir-arad\TwitterClient;

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

<!-- Packagist -->
[ico-version]: https://img.shields.io/packagist/v/narad1972/php-twitter-client.svg?style=flat-square
[link-packagist]: https://packagist.org/packages/narad1972/php-twitter-client

<!-- License -->
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square

<!-- Build -->
[ico-gitlab-build]: https://img.shields.io/gitlab/pipeline/narad1972/php-twitter-client/master
[link-gitlab-build]: https://gitlab.com/narad1972/php-twitter-client/-/releases

<!-- Pipeline status -->
[ico-gitlab-pipeline]: https://gitlab.com/narad1972/php-twitter-client/badges/master/pipeline.svg
[link-gitlab-pipeline]: https://gitlab.com/narad1972/php-twitter-client/commits/master

<!-- Coverage -->
<!-- [ico-scrutinizer]: https://img.shields.io/scrutinizer/coverage/g/narad1972/php-twitter-client.svg?style=flat-square -->
<!-- [link-scrutinizer]: https://scrutinizer-ci.com/g/narad1972/php-twitter-client/code-structure -->

[ico-gitlab-cov]: https://gitlab.com/narad1972/php-twitter-client/badges/master/coverage.svg
[link-gitlab-cov]: https://gitlab.com/narad1972/php-twitter-client/-/graphsmaster/charts

<!-- Quality -->
[ico-code-quality]: https://img.shields.io/scrutinizer/g/narad1972/php-twitter-client.svg?style=flat-square
[link-code-quality]: https://scrutinizer-ci.com/g/narad1972/php-twitter-client

<!-- Downloads -->
[ico-downloads]: https://img.shields.io/packagist/dt/narad1972/php-twitter-client.svg?style=flat-square
[link-downloads]: https://packagist.org/packages/narad1972/php-twitter-client

<!-- Credits -->
[link-author]: https://gitlab.com/narad1972
[link-contributors]: https://gitlab.com/narad1972/php-twitter-client/-/graphs/master
