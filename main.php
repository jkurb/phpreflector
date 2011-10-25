<?php
/**
 * Точка входа
 *
 * PHP version 5
 *
 * @package
 * @author   Eugene Kurbatov <ekur@i-loto.ru>
 */

set_include_path(get_include_path() . PATH_SEPARATOR .
    realpath(dirname(__FILE__)) . "/include");


require_once "include/EntityMetaManager.php";


/**
 * @column id
 * @column id, type="int(10)", unsigned=true
 * @column type="varchar(256)", unique=false, nullable=false
 * @column type="datetime", allowNull=true, default="val"
 * @column name="myfield", type="varchar(256)", unique=false, nullable=false
 */


EntityMetaManager::init(require_once "config.php");

$ent = EntityMetaManager::createFromTable("user");

EntityMetaManager::saveToFile($ent, "tests/fixtures/User.php");


//$clsRef = ClassReflector::createFromFile("tests/fixtures/User.php", "User");

//$clsRef->reflectToFile("res/CopyUser.php", "CopyUser");

//$clsRef->reflectToTable(require "config.php", "user");


