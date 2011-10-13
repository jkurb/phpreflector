<?php
/**
 * Интерфейс
 *
 * PHP version 5
 *
 * @package
 * @author   Eugene Kurbatov <ekur@i-loto.ru>
 */

interface IClassReflector
{
	public static function createFromFile($path, $classname);

	public static function createFromTable($config, $tbname);

	public function reflectToTable($config, $tbname);

	public function reflectToFile($path, $classname);
}