<?php
/**
 * TODO: Добавить здесь комментарий
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

set_include_path(get_include_path() . PATH_SEPARATOR .
    realpath(dirname(__FILE__)) . "/include");


require_once "include/Zend/Console/Getopt.php";
require_once "include/MyCmdArguments.php";
require_once "include/Commands/Update.php";
require_once "include/Commands/Help.php";
require_once "include/Commands/Create.php";

try
{
	$args = new MyCmdArguments($argv);
	$args->parse();

	$className = $args->getCommnad();
	$params = $args->getParams();

	$ref = new ReflectionClass(ucfirst($className));

	/** @var $command BaseCommand */
	$command = $ref->newInstanceArgs($params);

	$command->execute();
}
catch (Exception $e)
{
    echo "Error: " . $e->getMessage();
}