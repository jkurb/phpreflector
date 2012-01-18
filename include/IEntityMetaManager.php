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

	public static function createFromTable($tbname);

	public static function saveToTable($tbname);

	public static function mergeAndSaveToFile($path, $classname);
}