<?php
/**
 * unit-curl:/testcase/cookie.php
 *
 * @created   2020-03-01
 * @version   1.0
 * @package   unit-curl
 * @author    Tomoaki Nagahara <tomoaki.nagahara@gmail.com>
 * @copyright Tomoaki Nagahara All right reserved.
 */

/** namespace
 *
 */
namespace OP;

/* @var $curl UNIT\Curl */
$curl = Unit::Singleton('Curl');

//	...
$scheme = 'http';
$host   = $_SERVER['HTTP_HOST'];
$path   = ConvertURL('app:/api/testcase/?sleep=0');
$url    = "{$scheme}://{$host}{$path}";
D($url);

//	...
$option = [
	'cookie' => realpath(__DIR__.'/../cookie')."/$host",
];
D($option);

//	...
$json = $curl->Get($url, [], $option);
D($json);

//	...
$json = json_decode($json, true);
D($json);
