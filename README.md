# Oauth2 Server Laravel
[![Build Status](https://scrutinizer-ci.com/g/mvdstam/oauth2-server-laravel/badges/build.png?b=master)](https://scrutinizer-ci.com/g/mvdstam/oauth2-server-laravel/build-status/master) [![Code Coverage](https://scrutinizer-ci.com/g/mvdstam/oauth2-server-laravel/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/mvdstam/oauth2-server-laravel/?branch=master) [![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/mvdstam/oauth2-server-laravel/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/mvdstam/oauth2-server-laravel/?branch=master)

[![SensioLabsInsight](https://insight.sensiolabs.com/projects/0d91d830-148e-4013-8c6c-e128b8efc4de/mini.png)](https://insight.sensiolabs.com/projects/0d91d830-148e-4013-8c6c-e128b8efc4de)

**Please note that this repository is under construction. This package is NOT ready for usage yet.**

This package forms the link between your Laravel (or Lumen) application and the [league/oauth2-server](https://github.com/thephpleague/oauth2-server) package.

The goals of this package are as follows:

- Provide an **easy** and **non-intrusive** way to provide Oauth2 authorization for your application
- Have sensible defaults in place that will work for most cases, but at the same time offer flexibility for those who desire it
- Stay as close to the [league/oauth2-server](https://github.com/thephpleague/oauth2-server) code as possible

## Why not [Laravel Passport](https://github.com/laravel/passport)?

This package aims to be a more flexible, more advanced and more loosely coupled alternative to Laravel Passport.
In addition, this package aims to be compatible with Laravel/Lumen 5.1 and up.

## Installation

Simply install this package with composer:

```sh
$ composer require mvdstam/oauth2-server-laravel
```

## Framework compatibility

| Framework version | Build status                                                                                                                                                   |
|-------------------|----------------------------------------------------------------------------------------------------------------------------------------------------------------|
| Laravel 5.1       | [![Build Status](https://travis-ci.org/mvdstam/oauth2-server-laravel-tests.svg?branch=laravel-5.1)](https://travis-ci.org/mvdstam/oauth2-server-laravel-tests) |
| Laravel 5.2       | [![Build Status](https://travis-ci.org/mvdstam/oauth2-server-laravel-tests.svg?branch=laravel-5.2)](https://travis-ci.org/mvdstam/oauth2-server-laravel-tests) |
| Laravel 5.3       | [![Build Status](https://travis-ci.org/mvdstam/oauth2-server-laravel-tests.svg?branch=laravel-5.3)](https://travis-ci.org/mvdstam/oauth2-server-laravel-tests) |
| Laravel 5.4       | [![Build Status](https://travis-ci.org/mvdstam/oauth2-server-laravel-tests.svg?branch=laravel-5.4)](https://travis-ci.org/mvdstam/oauth2-server-laravel-tests) |