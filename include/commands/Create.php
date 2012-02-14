<?php
/**
 * Создание сущности
 *
 * PHP version 5
 *
 * @package
 * @author   Eugene Kurbatov <ekur@i-loto.ru>
 */

class Create extends BaseCommand
{
	public function getDeclaration()
	{
		return array
		(
			"class|cls|c=w" => "Class file name",
			"table|tbl|t=w" => "Table name"
		);
	}

	public function execute()
	{
		// TODO: Implement execute() method.
	}
}
