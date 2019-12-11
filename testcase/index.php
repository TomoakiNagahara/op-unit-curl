<?php
/**
 * unit-curl:/testcase/index.php
 *
 * @created   2019-04-06
 * @moved     2019-12-11   Separated from "module-testcase"
 * @version   1.0
 * @package   module-testcase
 * @author    Tomoaki Nagahara <tomoaki.nagahara@gmail.com>
 * @copyright Tomoaki Nagahara All right reserved.
 */

/** namespace
 *
 * @created   2019-04-06
 */
namespace OP;

/* @var $app  UNIT\App  */
/* @var $curl UNIT\Curl */
$curl = $app->Unit('Curl');

//	...
$scheme = 'http';
$host   = $_SERVER['HTTP_HOST'];
$path   = $app->URL('app:/api/');
$url    = "{$scheme}://{$host}{$path}";

//	...
D( json_decode($curl->Get( $url, ['get'=>['user_id','nickname']]), true) );

//	...
D( json_decode($curl->Post($url, ['get'=>['user_id','nickname']]), true) );

//	...
D($curl);
