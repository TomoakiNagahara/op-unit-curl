<?php
/**
 * unit-curl:/testcase/index.php
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

/* @var $app UNIT\App */
$args = Unit::Singleton('Router')->Args();

//	...
include('index.phtml');

//	...
if( $arg = $args[2] ?? null ){
	//	...
	$arg = trim($arg, '.');

	//	...
	$file = __DIR__.'/'.$arg.'.php';

	//	...
	if( file_exists($file) ){
		Unit::Singleton('Template')->__TEMPLATE($file);
	}
}

return;

//	...
$curl = Unit::Instantiate('Curl');

//	...
$curl->Post();
