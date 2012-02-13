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
            "dbname"   => "temp",
            "driver_options" => array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8")
        )
    ),
    "classTemplate" => realpath(dirname(__FILE__)) . "/templates/class.tpl",

    "fieldTemplate" => realpath(dirname(__FILE__)) . "/templates/field.tpl",

	"author" => "Eugene Kurbatov",

	"defaultIdType" => "int(11) unsigned",
);
