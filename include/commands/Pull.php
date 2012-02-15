<?php
/**
 * TODO: Добавить здесь комментарий
 *
 * PHP version 5
 *
 * @package
 * @author   Eugene Kurbatov <ekur@i-loto.ru>
 */

class Pull extends BaseCommand
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
			$ent = OrmManager::createFromTable($tblName);
			OrmManager::mergeAndSaveToFile($ent, $this->params->file);
		}
	}
}
