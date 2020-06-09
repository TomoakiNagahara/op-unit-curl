<?php
/** op-unit-curl:/function/GetCookieFilePath.php
 *
 * @created   2020-06-09
 * @version   1.0
 * @package   op-unit-curl
 * @author    Tomoaki Nagahara <tomoaki.nagahara@gmail.com>
 * @copyright Tomoaki Nagahara All right reserved.
 */

/** namespace
 *
 */
namespace OP\UNIT\CURL;

/** GetCookieFilePath
 *
 * @param     string
 * @return    string
 */
function GetCookieFilePath($url)
{
	//	...
	if(!$host = parse_url($url)['host'] ?? null ){
		return false;
	}

	//	...
	return realpath(__DIR__.'/../cookie').'/'.$host;
}
