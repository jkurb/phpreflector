<?php
/**
 * Интерфейс
 *
 * PHP version 5
 *
 * @package
 * @author   Eugene Kurbatov <ekur@i-loto.ru>
 */

interface IEntityMetaManager
{
	public static function createFromFile($path, $classname);

	public static function createFromTable($config, $tbname);

	public static function saveToTable($config, $tbname);

	public static function saveToFile($path, $classname);
}