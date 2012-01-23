<?php
set_include_path(get_include_path() . PATH_SEPARATOR .
    realpath(dirname(__FILE__)) . "/../include");

// Autoload
spl_autoload_register(function($className) {
	$file = strtr($className, '\\_', DIRECTORY_SEPARATOR . DIRECTORY_SEPARATOR) . '.php';
	if (!function_exists('stream_resolve_include_path') || false !== stream_resolve_include_path($file)) {
		require_once $file;
	}
});


require_once "../include/EntityMetaManager.php";

class EntityMetaManagerTest extends PHPUnit_Framework_TestCase
{
	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 */
	protected function setUp()
	{
	}

	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 */
	protected function tearDown()
	{
	}

	public function testCreateFromFile()
	{
		$ent = EntityMetaManager::createFromFile("fixtures/Entity.php");

		$this->assertEquals("Entity", $ent->name);
		$this->assertEquals("Базовый класс для всех сущностей", $ent->comment);
		$this->assertEquals(4, count($ent->fields));
		$this->assertEquals(6, count($ent->constants));
		$this->assertEquals("5471d5a458908301b6cc8ef8a2b7183f", md5($ent->strMethods));

		$this->assertEquals("entityTable", $ent->fields[0]->name);
		$this->assertEquals(null, $ent->fields[0]->default);
		$this->assertEquals("Содержит наименование таблицы в БД, где хранятся сущности этого типа. Не является атрибутом сущности",
			$ent->fields[0]->comment);
		$this->assertEquals(true, $ent->fields[0]->allowNull);
		$this->assertEquals(false, $ent->fields[0]->isColomn);
		$this->assertEquals(null, $ent->fields[0]->type);
		$this->assertEquals(true, $ent->fields[0]->isPublic);

		$this->assertEquals("testField", $ent->fields[3]->name);
		$this->assertEquals(true, $ent->fields[3]->allowNull);
		$this->assertEquals("Тестовое поле", $ent->fields[3]->comment);
		$this->assertEquals("varchar(255)", $ent->fields[3]->type);
		$this->assertEquals(true, $ent->fields[3]->isColomn);
		$this->assertEquals(false, $ent->fields[3]->isAutoincremented);

		$this->assertEquals(true, $ent->fields[1]->isColomn);
		$this->assertEquals(true, $ent->fields[1]->isId);
		$this->assertEquals(true, $ent->fields[1]->isAutoincremented);
		$this->assertEquals(false, $ent->fields[1]->allowNull);
		$this->assertEquals("int(11) unsigned", $ent->fields[1]->type);

	}
}
