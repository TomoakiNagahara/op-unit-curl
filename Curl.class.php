<?php
/** op-unit-curl:/Curl.class.php
 *
 * @created   2017-06-01
 * @version   1.0
 * @package   op-unit-curl
 * @author    Tomoaki Nagahara <tomoaki.nagahara@gmail.com>
 * @copyright Tomoaki Nagahara All right reserved.
 */

/** namespace
 *
 * @created   2018-07-02
 */
namespace OP\UNIT;

/** Used class
 *
 */
use OP\OP_CORE;
use OP\OP_UNIT;
use OP\OP_DEBUG;
use OP\IF_UNIT;
use OP\Config;
use OP\UNIT\CURL\File;
use function OP\ConvertURL;
use function OP\UNIT\CURL\GetReferer;
/*
use function OP\UNIT\CURL\GetCookieFilePath;
*/

/** Curl
 *
 * @created   2017-06-01
 * @version   1.0
 * @package   unit-curl
 * @author    Tomoaki Nagahara <tomoaki.nagahara@gmail.com>
 * @copyright Tomoaki Nagahara All right reserved.
 */
class Curl implements IF_UNIT
{
	/** trait.
	 *
	 */
	use OP_CORE, OP_UNIT, OP_DEBUG;

	/** Last error message.
	 *
	 * @var string
	 */
	static private $_errors;

	/** Parse header string.
	 *
	 * @param  string $header
	 * @return array  $header
	 */
	static private function _Header($headers)
	{
		//	...
		$result = [];

		//	...
		foreach( explode("\r\n", $headers) as $header ){
			if( strlen($header) === 0 ){
				//	...
			}else if( $pos = strpos($header, ':') ){
				//	...
				$key = substr($header, 0, $pos  );
				$val = substr($header,    $pos+1);

				//	...
				$result[strtolower($key)] = trim($val);

				//	Content-Type
				if( strtolower($key) === 'content-type' ){
					//	MIME, Charset
					list($mime, $charset) = explode(';', $val.';');

					//	MIME
					$result['mime']    = trim($mime);

					//	Charset
					if( $charset ){
						list($key, $val) = explode('=', trim($charset));
						$result[$key] = $val;
					};
				};
			}else if( strpos($header, 'HTTP/1.') === 0 ){
				$result['status'] = explode(' ', $header)[1];
			}else{
				$result[] = $header;
			};
		};

		//	...
		return $result;
	}

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
	 * @param  array   $option
	 * @return string  $body
	 */
	static private function _Execute($url, $post, $option=[])
	{
		//	...
		$config = Config::Get('curl');

		//	...
		$option = array_merge($config, $option);

		//	...
		$format     = $option['format']  ?? null; // Json, Xml
		$referer    = $option['referer'] ?? null; // Specified referer.
		$has_header = $option['header']  ?? null; // Return request and response headers.

		// Timeout second.
		$timeout    = $option['timeout'] ?? $config['ua'] ??   10;

		// Specified User Agent.
		$ua         = $option['ua']      ?? $config['ua'] ?? null;

		//	Content Type
		switch( $format ){
			default:
				$content_type = 'application/x-www-form-urlencoded';
		};

		//	Get referer at current app uri.
		if( $referer === true ){
			require_once(__DIR__.'/function/GetReferer.php');
			$referer = GetReferer();
		}

		//	Data serialize.
		$data = $post ? self::_Data($post, $format): null;

		//	HTTP Header
		$header = [];
		$header[] = "Content-Type: {$content_type}";
		$header[] = "Content-Length: ".strlen($data);

		//	Cookie is direct string.
		if( $cookie = $option['cookie_string'] ?? null ){
			$header[] = "Cookie: $cookie";
		}

		//	Referer
		if( $referer ){
			$header[] = "Referer: $referer";
		}

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
		$curl = curl_init();

		//	...
		if( $has_header ){
			curl_setopt($curl, CURLOPT_HEADER, true);
		};

		//	POST
		if( $post !== null ){
			curl_setopt( $curl, CURLOPT_CUSTOMREQUEST , 'POST' );
			curl_setopt( $curl, CURLOPT_POST          ,  true  );
			curl_setopt( $curl, CURLOPT_POSTFIELDS    ,  $data );
		};

		//	SSL
		if( strpos($url, 'https://') === 0 ){
			curl_setopt( $curl, CURLOPT_SSL_VERIFYPEER, true);
			curl_setopt( $curl, CURLOPT_CAINFO, __DIR__.'/cacert.pem');
		};

		//	Cookie read/write
		foreach(['cookie_read' => CURLOPT_COOKIEFILE, 'cookie_write' => CURLOPT_COOKIEJAR] as $key => $var){
			//	...
			if(!$path = $option[$key] ?? null ){
				continue;
			}

			//	...
			if(!file_exists($path)){
				require_once(__DIR__.'/File.class.php');
				File::Create($path);
			};

			//	...
			curl_setopt($curl, $var, $path);
		}

		//	...
		$curl_option = [
			CURLOPT_URL            =>  $url,
			CURLOPT_HTTPHEADER     =>  $header,
			CURLOPT_USERAGENT      =>  $ua,
			CURLOPT_REFERER        =>  $referer,
			CURLOPT_RETURNTRANSFER =>  true,
			CURLOPT_TIMEOUT        =>  $timeout,
		];

		//	...
		curl_setopt_array($curl, $curl_option);

		//	...
		if(!$body = curl_exec($curl)){
			$body = curl_getinfo($curl);
		};

		//	...
		if( $errno = curl_errno($curl) ){
		//	curl_getinfo($curl)
			self::$_errors[] = sprintf('%s: %s, %s', $errno, $url, $timeout);
		};

		//	Return ['head','body'] array.
		if( $has_header ){
			$body = curl_exec($curl);
			$size = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
			return [
					'head' => self::_Header(
					          substr($body, 0, $size) ),
					'body' => substr($body,    $size),
			];
		};

		//	Return body string only.
		return $body;
	}

	/** Separate error routine.
	 *
	 * @created  2019-08-22
	 * @param    integer     $error   is Curl error code
	 * @param    integer     $info    is Curl transfer information
	 * @param    string      $url     is fetch URL
	 * @param    integer     $timeout is timeout sec
	 */
	static private function _ExecuteError($errno, $info, $url, $timeout)
	{
		//	...
		switch( $errno ){
			case CURLE_OK:
				break;

			case CURLE_URL_MALFORMAT:
				self::$_errors[] = 'The URL was not properly formatted.';
				break;

			case CURLE_COULDNT_RESOLVE_HOST:
				self::$_errors[] = 'Couldn\'t resolve host. The given remote host was not resolved.';
				break;

			case 28:
			case OPERATION_TIMEOUTED:
				self::$_errors[] = "Response is timeout. ({$timeout} sec.)";
				break;

			case 60:
			case CURLE_PEER_FAILED_VERIFICATION:
				self::$_errors[] = 'The remote server\'s SSL certificate or SSH md5 fingerprint was deemed not OK.';
				break;

			default:
				self::$_errors[] = "Response is error. ({$errno}, {$url})";
				break;
		}

		//	...
		switch( $code = $info['http_code'] ){
			case 0:
				return false;

			case 200:
			case 302:
			case 403:
			case 404:
			case 405:
				break;

			/*
			case 301:
				if(!$body ){
					$body = $info;
				};
				break;
			*/

			default:
				Notice::Set("Http status code is {$code}.");
				break;
		}
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
	 * @param  string $option
	 * @return string $body
	 */
	static function Get($url, $data=null, $option=[])
	{
		//	...
		if( $data ){
			//	...
			if( strpos($url, '?') ){
				list($url, $query) = explode('?', $url);
				parse_str($query, $query);
				$data = array_merge($query, $data);
			}

			//	...
			$url .= '?'.http_build_query($data);
		}

		//	...
		return self::_Execute($url, null, $option);
	}

	/** Post method.
	 *
	 * @param  string $url
	 * @param  array  $post
	 * @param  string $option
	 * @return string $body
	 */
	static function Post($url, $post=[], $option=null)
	{
		return self::_Execute($url, $post, $option);
	}

	/** Return last error message.
	 *
	 * @return string
	 */
	static function Error()
	{
		return array_shift(self::$_errors[]);
	}
}
