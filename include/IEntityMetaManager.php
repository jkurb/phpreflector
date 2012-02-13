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
	public static function createFromFile($path);

	public static function createFromTable($tbname);

	public static function saveToTable($entityMeta, $tbname);

	public static function saveToFile($entityMeta, $path);


}