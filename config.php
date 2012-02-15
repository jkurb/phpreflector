<?php
/**
 * Файл конфигурации
 *
 * PHP version 5
 *
 * @package
 * @author   Eugene Kurbatov <ekur@i-loto.ru>
 */

return array(
    "database" => array(
        "adapter" => "pdo_mysql",
        "params"  => array(
            "host"     => "localhost",
            "username" => "root",
            "password" => "toor",
            "dbname"   => "payanyway",
            "driver_options" => array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8")
        )
    ),

    "templatesDir" => realpath(dirname(__FILE__)) . "/include/templates",

	"author" => "Eugene Kurbatov <ekur@i-loto.ru>",

	"defaultIdType" => "int(11) unsigned",

	"processDir" => "/var/www/payanyway.loc/www/framework/core",
);