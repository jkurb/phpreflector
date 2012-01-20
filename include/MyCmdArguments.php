<?php
/**
 * TODO: Добавить здесь комментарий
 *
 * PHP version 5
 *
 * @package
 * @author   Eugene Kurbatov <ekur@i-loto.ru>
 */

require_once "CmdArguments.php";

class MyCmdArguments extends CmdArguments
{
	public function parse()
	{
		if (!isset($this->cmdArgs[1]))
		{
			throw new Exception("Command not specified");
		}

		$this->commnad = $this->cmdArgs[1];
		unset($this->cmdArgs[1]);
		$this->params = $this->cmdArgs;
	}
}
