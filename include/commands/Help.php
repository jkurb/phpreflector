<?php
/**
 * Описание команд
 *
 * PHP version 5
 *
 * @package
 * @author   Eugene Kurbatov <ekur@i-loto.ru>
 */

class Help extends BaseCommand
{
	public function execute()
	{
		echo $this->getDescription();
	}
}
