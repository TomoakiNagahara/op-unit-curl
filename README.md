Unit of CURL
===

# Overview

 Supported

 * SSL
 * Cookie
 * Session
 * Referer

# Usage

## Instantiate

```php
$curl = OP\Unit::Instantiate('Curl');
```

## Get

```php
$result = $curl->Get('http://example.com');
```

## Post

```php
$result = $curl->Post('http://example.com/login', ['user'=>'aaa','password'=>'bbb']);
```

## Post at JSON

```php
$result = $curl->Post('http://example.com/login', ['user'=>'aaa','password'=>'bbb'], ['format'=>'json']);
```

## Post at XML

```php
$result = $curl->Post('http://example.com/login', '<xml><value user="aaa" password="bbb" /></xml>', ['format'=>'xml']);
```

## Referer

```php
//  Auto generate current URI referer.
$option = ['referer' => true];
$result = $curl->Get('http://example.com', null, $option);
```

```php
//  Specify referer.
$option = ['referer' => 'http://example.com/?test=1'];
$result = $curl->Get('http://example.com', null, $option);
```

## Cookie keeping

 Keeping a cookie is keeping a session.

```php
//  Specify a file to read and write cookies.
$option = [
  'cookie_read'  => '/tmp/cookie.txt',
  'cookie_write' => '/tmp/cookie.txt',
];
$result = $curl->Get('http://example.com', null, $option);
```

```php
//  You can directly specify the cookie string.
$option = [
  'cookie_string' => 'key1=var1; key2=var2',
];
$result = $curl->Get('http://example.com', null, $option);
```

## Header

 Return response header.

```php
$result = $curl->Get('http://example.com', null, ['header'=>true]);
```


