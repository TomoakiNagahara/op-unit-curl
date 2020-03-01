<?php
/**
 * unit-curl:/function/GetReferer.php
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
namespace OP\UNIT\CURL;

/** GetReferer
 *
 * @created   2020-03-01
 * @version   1.0
 * @package   unit-curl
 * @author    Tomoaki Nagahara <tomoaki.nagahara@gmail.com>
 * @copyright Tomoaki Nagahara All right reserved.
 */
function GetReferer()
{
	$scheme = empty($_SERVER['HTTPS']) ? 'http': 'https';
	$host   = $_SERVER['HTTP_HOST']   ?? 'localhost';
	$uri    = $_SERVER['REQUEST_URI'] ?? '/';
	$uri    = explode('?', $uri)[0]; // Remove url query.
	return "{$scheme}://{$host}{$uri}";
}
