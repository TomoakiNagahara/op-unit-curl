<?php
/** op-unit-curl:/testcase/get.php
 *
 * @created   2020-03-01
 * @version   1.0
 * @package   op-unit-curl
 * @author    Tomoaki Nagahara <tomoaki.nagahara@gmail.com>
 * @copyright Tomoaki Nagahara All right reserved.
 */

/** namespace
 *
 */
namespace OP;

/* @var $curl UNIT\Curl */
$curl = Unit('Curl');

//	...
$scheme = 'http';
$host   = $_SERVER['HTTP_HOST'];
$path   = ConvertURL('app:/api/testcase/');
$url    = "{$scheme}://{$host}{$path}";
D($url);

//	...
$json = $curl->Get($url);
D($json);

//	...
$json = json_decode($json, true);
D($json);
