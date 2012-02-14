<?php
/**
 * Доступ к конфигам
 *
 * PHP version 5
 *
 * @package
 * @author   Eugene Kurbatov <ekur@i-loto.ru>
 */

class Config
{
	private static $instance = null;

	private static $configPath = null;

	public static function init($configPath)
	{
		self::$configPath = $configPath;
	}

	private function __construct()
	{
	}

	public static function getInstance()
	{
		if (self::$instance == null)
		{
			self::$instance = new Zend_Config(include self::$configPath);
		}
		return self::$instance;
	}
}
