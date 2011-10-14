<?php
/**
 * Точка входа
 *
 * PHP version 5
 *
 * @package
 * @author   Eugene Kurbatov <ekur@i-loto.ru>
 */
require_once "include/ClassReflector.php";


/**
 * @column(id)
 * @column(id, type="int(10)", unsigned=true)
 * @column(type="varchar(256)", unique=false, nullable=false)
 * @column(type="datetime", nullable=true)
 * @column(name="myfield", type="varchar(256)", unique=false, nullable=false)
 */

$clsRef = ClassReflector::createFromTable(require "config.php", "user");

//$clsRef = ClassReflector::createFromFile("res/User.php", "User");

//$clsRef->reflectToFile("res/CopyUser.php", "CopyUser");

//$clsRef->reflectToTable(require "config.php", "user");


