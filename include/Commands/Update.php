<?php
/**
 * Команда обновления сущности
 *
 * PHP version 5
 *
 * @package
 * @author  Eugene Kurbatov <ekur@i-loto.ru>
 */

require_once "BaseCommand.php";
require_once realpath(dirname(__FILE__)) . "/../Zend/Console/Getopt.php";

/**
 * Usage:
 *    update --class=User --table=user
 *    update --cls=User --tbl=user
 *    update -c User -t user
 */

class Update extends BaseCommand
{
	const SOURCE_CLASS = "SOURCE_CLASS";
	const SOURCE_TABLE = "SOURCE_TABLE";

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
		// class -> table
		if ($this->getSource() == self::SOURCE_CLASS)
		{
			echo "class -> table\n";
		}
		// table -> class
		else if ($this->getSource() == self::SOURCE_TABLE)
		{
			echo "table -> class\n";
		}
	}

	private function getSource()
	{
		$opts = $this->params->getOptions();
		if ($opts[0] == "class")
		{
			return self::SOURCE_CLASS;
		}
		else if ($opts[0] == "table")
		{
			return self::SOURCE_TABLE;
		}
		else
		{
			throw new Exception("Undefined options");
		}
	}
}
