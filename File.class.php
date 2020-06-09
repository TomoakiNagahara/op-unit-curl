<?php
/** op-unit-curl:/File.class.php
 *
 * @created   2019-06-26
 * @version   1.0
 * @package   op-unit-curl
 * @author    Tomoaki Nagahara <tomoaki.nagahara@gmail.com>
 * @copyright Tomoaki Nagahara All right reserved.
 */

/** namespace
 *
 * @created   2019-06-26
 */
namespace OP\UNIT\CURL;

/** use
 *
 */
use OP\OP_CORE;

/** File
 *
 * @created   2019-06-26
 * @version   1.0
 * @package   unit-curl
 * @author    Tomoaki Nagahara <tomoaki.nagahara@gmail.com>
 * @copyright Tomoaki Nagahara All right reserved.
 */
class File
{
	/** trait.
	 *
	 */
	use OP_CORE;

	/** Create directory.
	 *
	 * @created 2019-06-26
	 * @param   string     $path
	 * @throws \Exception
	 */
	static function Mkdir($path)
	{
		//	Get current umask.
		$umask = umask();

		//	Overwrite new umask.
		umask(0);

		//	Create new directory.
		$io = mkdir($path, 0755, true);

		//	Restoration.
		umask($umask);

		//	...
		if(!$io ){
			throw new \Exception("Create directory was failure. ($path)");
		};
	}

	/** Create file.
	 *
	 * @created 2019-06-26
	 * @param   string     $path
	 * @throws \Exception
	 */
	static function Create($path)
	{
		//	...
		if(!file_exists($dir = dirname($path)) ){
			self::Mkdir($dir);
		};

		//	...
		if(!touch($path) ){
			throw new \Exception("Create file has failure. ($path)");
		};
	}
}
