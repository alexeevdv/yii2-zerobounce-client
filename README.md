# yii2-zerobounce-client

[![Build Status](https://travis-ci.com/alexeevdv/yii2-zerbounce-client.svg?branch=master)](https://travis-ci.com/alexeevdv/yii2-zerbounce-client) 
[![codecov](https://codecov.io/gh/alexeevdv/yii2-zerbounce-client/branch/master/graph/badge.svg)](https://codecov.io/gh/alexeevdv/yii2-zerbounce-client)
![PHP 7.1](https://img.shields.io/badge/PHP-7.1-green.svg) 
![PHP 7.2](https://img.shields.io/badge/PHP-7.2-green.svg)
![PHP 7.3](https://img.shields.io/badge/PHP-7.3-green.svg)

Yii client for https://www.zerobounce.net API

API docs are available at https://www.zerobounce.net/docs/

## Installation

The preferred way to install this extension is through [composer](https://getcomposer.org/download/).

Either run

```bash
$ composer require alexeevdv/yii2-zerobounce-client "^1.0"
```

or add

```
"alexeevdv/yii2-zerobounce-client": "^1.0"
```

to the ```require``` section of your `composer.json` file.

## Configuration

```php
'container' => [
    'singletons' => [
        alexeevdv\yii\zerobounce\ClientInterface::class => [
            'class' => alexeevdv\yii\zerobounce\Client::class,
            'apiKey' => 'a95c530a7af5f492a74499e70578d150',         
        ],
    ],
],
```

## Usage

```php
$client = yii\di\Instance::ensure(alexeevdv\yii\zerobounce\ClientInterface::class);
$isValid = $client->isEmailValid('valid@example.com');
```
