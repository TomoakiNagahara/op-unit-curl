<?php
/**
 * unit-curl:/Curl.class.php
 *
 * @creation  2017-06-01
 * @version   1.0
 * @package   app-skeleton
 * @author    Tomoaki Nagahara <tomoaki.nagahara@gmail.com>
 * @copyright Tomoaki Nagahara All right reserved.
 */

/** namespace
 *
 * @creation  2018-07-02
 */
namespace OP\UNIT;

/** Used class
 *
 */
use OP\OP_CORE;
use OP\OP_UNIT;
use OP\OP_DEBUG;
use OP\IF_UNIT;
use function OP\ConvertURL;

/** Curl
 *
 * @creation  2017-06-01
 * @version   1.0
 * @package   app-skeleton
 * @author    Tomoaki Nagahara <tomoaki.nagahara@gmail.com>
 * @copyright Tomoaki Nagahara All right reserved.
 */
class Curl implements IF_UNIT
{
	/** trait.
	 *
	 */
	use OP_CORE, OP_UNIT, OP_DEBUG;

	/** Convert to string from array at post data.
	 *
	 * @param  array  $post
	 * @param  string $format
	 * @return string $data
	 */
	static private function _Data($post, $format=null)
	{
		switch( $format ){
			case 'json':
				$data = json_encode($post);
				break;

			default:
				//	Content-Type: application/x-www-form-urlencoded
				/*
				$temp = [];
				foreach( $post as $key => $val ){
					$temp[$key] = self::Escape($val);
				}
				*/
				$data = http_build_query($post, null, '&');
		}

		//	...
		return $data;
	}

	/** Execute to Curl.
	 *
	 * @param  string  $url
	 * @param  array   $post
	 * @param  string  $format
	 * @param  string  $ua
	 * @param  string  $referer
	 * @param  boolean $ssl
	 * @return string  $body
	 */
	static private function _Execute($url, $post=null, $format=null, $ua=null, $referer=true, $ssl=null)
	{
		//	...
		if( strpos($url, 'https://') === 0 ){
			self::__DebugSet('php', 'openssl.cafile='.ini_get('openssl.cafile'));
		};

		//	Content Type
		switch( $format ){
			default:
				$content_type = 'application/x-www-form-urlencoded';
		};

		//	...
		if( $referer === true ){
			$scheme = empty($_SERVER['HTTPS']) ? 'http': 'https';
			$host   = $_SERVER['HTTP_HOST']   ?? 'localhost';
			$uri    = $_SERVER['REQUEST_URI'];
			//	list($uri, $query) = explode('?', $uri.'?');
			$referer= "{$scheme}://{$host}{$uri}";
		};

		//	...
		$data = $post ? self::_Data($post, $format): null;

		//	...
		$header = [];
		$header[] = "Content-Type: {$content_type}";
		$header[] = "Content-Length: ".strlen($data);
		$header[] = "Referer: $referer";

		//	Check if installed PHP CURL.
		if(!defined('CURLOPT_URL') ){
			//	...
			D('PHP CURL is not installed.');

			//	...
			if( $post ){
				//	...
				$context = stream_context_create([
					'http' => [
						'method'  => 'POST',
						'header'  =>  implode("\r\n", $header),
						'content' => $data
					],
					/*
					'ssl' => [
						'verify_peer'      => false,
						'verify_peer_name' => false,
					],
					*/
				]);
			};

			//	...
			return file_get_contents($url, false, ($context ?? null));
		};

		//	...
		$option = [
			CURLOPT_URL            =>  $url,
			CURLOPT_HTTPHEADER     =>  $header,
			CURLOPT_USERAGENT      =>  $ua,
			CURLOPT_REFERER        =>  $referer,
			CURLOPT_RETURNTRANSFER =>  true,
			CURLOPT_TIMEOUT        =>  3,
		];

		//	...
		$curl = curl_init();
		curl_setopt_array($curl, $option);

		//	...
		if( $post ){
			curl_setopt( $curl, CURLOPT_CUSTOMREQUEST , 'POST' );
			curl_setopt( $curl, CURLOPT_POST          ,  true  );
			curl_setopt( $curl, CURLOPT_POSTFIELDS    ,  $data );
		}

		//	...
		$body  = curl_exec($curl);
		$info  = curl_getinfo($curl);
		$errno = curl_errno($curl);

		//	...
		switch( $errno ){
			case CURLE_OK:
				break;
			default:
		}

		//	...
		switch( $info['http_code'] ){
			case 200:
				break;
			default:
		}

		//	...
		return $body;
	}

	/** Escape of string.
	 *
	 * @param  string $string
	 * @return string $string
	 */
	static function Escape($string)
	{
		$string = preg_replace('/&/' , '%26', $string);
		$string = preg_replace('/ /' , '%20', $string);
		$string = preg_replace('/\t/', '%09', $string);
		$string = preg_replace('/\s/', '%20', $string);
		return $string;
	}

	/** Get method.
	 *
	 * @param  string $url
	 * @param  array  $data
	 * @return string $body
	 */
	static function Get($url, $data=null)
	{
		if( $data ){
			$url .= '?'.http_build_query($data);
		}

		//	...
		return self::_Execute($url);
	}

	/** Post method.
	 *
	 * @param  string $url
	 * @param  array  $post
	 * @param  string $format
	 * @return string $body
	 */
	static function Post($url, $post=null, $format=null)
	{
		return self::_Execute($url, $post, $format);
	}
}
