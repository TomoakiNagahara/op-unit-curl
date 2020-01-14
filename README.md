Unit of CURL
===

## Overview

 * Correspond SSL

## Usage

### Instantiate

```php
$curl = OP\Unit::Instantiate('Curl');
```

### Get

```php
$result = $curl->Get('http://example.com');
```

### Post

```php
$result = $curl->Post('http://example.com/login', ['user'=>'aaa','password'=>'bbb']);
```

### Post at JSON

```php
$result = $curl->Post('http://example.com/login', ['user'=>'aaa','password'=>'bbb'], ['format'=>'json']);
```

### Post at XML

```php
$result = $curl->Post('http://example.com/login', '<xml><value user="aaa" password="bbb" /></xml>', ['format'=>'xml']);
```

### Cookie keeping

 Keeping a cookie is keeping a session.

```php
$file_path = '/tmp/cookie.txt';
$result = $curl->Post('http://example.com/login', '['user'=>'aaa','password'=>'bbb']', ['cookie'=>$file_path]);
```

### Header

 Return response header.

```php
$result = $curl->Get('http://example.com', null, ['header'=>true]);
```
