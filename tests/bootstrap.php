<?php
/**
 * Bootstrap
 *
 * PHP version 5
 *
 * @package
 * @author   Eugene Kurbatov <ekur@i-loto.ru>
 */

set_include_path(get_include_path() . PATH_SEPARATOR .
    realpath(dirname(__FILE__)) . "/../include/lib" . PATH_SEPARATOR .
	realpath(dirname(__FILE__)) . "/../include");


// Autoload
spl_autoload_register(function($className)
{
	$file = strtr($className, '\\_', DIRECTORY_SEPARATOR . DIRECTORY_SEPARATOR) . '.php';
	if (!function_exists('stream_resolve_include_path') || false !== stream_resolve_include_path($file))
	{
		require_once $file;
	}
});

