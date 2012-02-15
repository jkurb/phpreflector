<?php
/**
 * Точка входа
 *
 * PHP version 5
 *
 * @package
 * @author   Eugene Kurbatov <ekur@i-loto.ru>
 */

/**
 * phpgen create --table=tblName
 * phpgen update --table=tblName --class=className
 * phpgen diff --table=tblName --class=className
 * phpgen help
 */

require_once "bootstrap.php";

try
{
	OrmManager::init("config.php");

	$args = new MyCmdArguments($argv);
	$args->parse();

	$className = $args->getCommnad();
	//$params = $args->getParams();

	$ref = new ReflectionClass(ucfirst($className));

	/** @var $command BaseCommand */
	$command = $ref->newInstanceArgs();

	$command->execute();
}
catch (Exception $e)
{
    echo "Error: " . $e->getMessage();
}