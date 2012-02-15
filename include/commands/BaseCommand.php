<?php
/**
 * Базовая класс команды
 *
 * PHP version 5
 *
 * @category PHP
 * @author   Eugene Kurbatov <eugene.kurbatov@gmail.com>
 * @version  BaseCommand.php 27.05.11 17:27 evkur
 * @link     nolink
 */

abstract class BaseCommand
{
   /**
	* @var Zend_Console_Getopt
	*/
	protected $params = null;

	public function __construct()
	{
		//late static binding
		$this->params = new Zend_Console_Getopt(static::getDeclaration());
		$this->params->parse();
	}

	public function getDescription()
    {
	    return $this->params->getUsageMessage();
    }

	public function getDeclaration()
	{
		return array();
	}

	abstract public function execute();
}
