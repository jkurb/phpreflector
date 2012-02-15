<?php
/**
 * TODO: Добавить здесь комментарий
 *
 * PHP version 5
 *
 * @package
 * @author   Eugene Kurbatov <ekur@i-loto.ru>
 */

class Push extends BaseCommand
{
	public function getDeclaration()
	{
		return array
		(
			"file|f=s" => "Class file path"
		);
	}

	public function execute()
	{
		if (isset($this->params->file))
		{
			$tblName = strtolower(basename($this->params->file, ".php"));
			$ent = OrmManager::createFromFile($this->params->file);
			OrmManager::mergeAndSaveToTable($ent, $tblName);
		}
	}
}
