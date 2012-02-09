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


class EntityMetaManagerTest extends PHPUnit_Framework_TestCase
{
	public function __construct()
	{
		EntityMetaManager::init(include "../config.php");
	}

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

		$this->assertEquals("Содержит наименование таблицы в БД, где хранятся сущности этого типа. Не является атрибутом сущности",
			$ent->fields[0]->comment);

		$this->assertNull($ent->fields[0]->default);
		$this->assertTrue($ent->fields[0]->allowNull);
		$this->assertFalse($ent->fields[0]->isColomn);
		$this->assertNull($ent->fields[0]->type);
		$this->assertTrue($ent->fields[0]->isPublic);

		$this->assertEquals("int(11) unsigned", $ent->fields[1]->type);

		$this->assertTrue($ent->fields[1]->isColomn);
		$this->assertTrue($ent->fields[1]->isId);
		$this->assertTrue($ent->fields[1]->isAutoincremented);
		$this->assertFalse($ent->fields[1]->allowNull);

		$this->assertEquals("testField", $ent->fields[3]->name);
		$this->assertEquals("Тестовое поле", $ent->fields[3]->comment);
		$this->assertEquals("varchar(255)", $ent->fields[3]->type);

		$this->assertTrue($ent->fields[3]->allowNull);
		$this->assertTrue($ent->fields[3]->isColomn);
		$this->assertFalse($ent->fields[3]->isAutoincremented);
	}

	public function testCreateFromTable()
	{
		$ent = EntityMetaManager::createFromTable("user");

		$this->assertEquals("User", $ent->name);
		$this->assertEquals("Таблица содержит информацию о типах пользователей Покупатель", $ent->comment);
		$this->assertEquals(7, count($ent->fields));

		$this->assertEquals("id", $ent->fields[0]->name);
		$this->assertEquals("Id пользовтаеля", $ent->fields[0]->comment);
		$this->assertEquals("int(10)", $ent->fields[0]->type);

		$this->assertTrue($ent->fields[0]->isId);
		$this->assertFalse($ent->fields[0]->allowNull);
		$this->assertTrue($ent->fields[0]->isColomn);
		$this->assertTrue($ent->fields[0]->isPublic);
		$this->assertTrue($ent->fields[0]->isPrimaryKey);
		$this->assertTrue($ent->fields[0]->isAutoincremented);

		$this->assertEquals("email", $ent->fields[1]->name);
		$this->assertEquals("Email пользователя", $ent->fields[1]->comment);
		$this->assertEquals("varchar(255)", $ent->fields[1]->type);

		$this->assertFalse($ent->fields[1]->isId);
		$this->assertTrue($ent->fields[1]->allowNull);
		$this->assertTrue($ent->fields[1]->isColomn);
		$this->assertTrue($ent->fields[1]->isPublic);
		$this->assertFalse($ent->fields[1]->isPrimaryKey);
		$this->assertFalse($ent->fields[1]->isAutoincremented);

		$this->assertNull($ent->strMethods);
	}

	public function testSaveToTable()
	{
		//EntityMetaManager::saveToTable();
	}
}
