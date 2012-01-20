<?php
/**
 * TODO: Добавить здесь комментарий
 *
 * PHP version 5
 *
 * @package
 * @author   Eugene Kurbatov <ekur@i-loto.ru>
 */

abstract class CmdArguments
{
	protected $commnad;

	protected $params = array();

	public $cmdArgs;

	public function __construct($args = null)
	{
		$this->cmdArgs = $args;
	}

	public function setCmdArgs($args)
	{
		$this->cmdArgs = $args;
	}

	abstract public function parse();

	public function getCommnad()
	{
		return $this->commnad;
	}

	public function getParams()
	{
		return $this->params;
	}
}