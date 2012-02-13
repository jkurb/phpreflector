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
	private $db = null;

	public function __construct()
	{
		$this->db = new PDO("mysql:dbname=temp;host=localhost", "root", "toor");
		EntityMetaManager::init();
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
		$ent = EntityMetaManager::createFromFile("fixtures/entities/User.php");

		$this->assertEquals("user", $ent->name);
		$this->assertEquals("Таблица содержит информацию о типах пользователей Покупатель", $ent->comment);
		$this->assertEquals(10, count($ent->fields));
		$this->assertEquals(8, count($ent->constants));
		$this->assertEquals("d46125e15f318e34f3b10ca73e71db68", md5($ent->strMethods));

		$this->assertEquals("entityTable", $ent->fields[0]->name);

		$this->assertEquals("Содержит наименование таблицы в БД, где хранятся сущности этого типа. Не является атрибутом сущности",
			$ent->fields[0]->comment);

		$this->assertEquals("user", $ent->fields[0]->default);
		$this->assertTrue($ent->fields[0]->allowNull);
		$this->assertFalse($ent->fields[0]->isColomn);
		$this->assertNull($ent->fields[0]->type);
		$this->assertTrue($ent->fields[0]->isPublic);

		$this->assertEquals("int(11) unsigned", $ent->fields[8]->type);

		$this->assertTrue($ent->fields[8]->isColomn);
		$this->assertTrue($ent->fields[8]->isId);
		$this->assertTrue($ent->fields[8]->isAutoincremented);
		$this->assertFalse($ent->fields[8]->allowNull);
		$this->assertTrue($ent->fields[8]->isInherited);

		$this->assertEquals("email", $ent->fields[2]->name);
		$this->assertEquals("Email пользователя", $ent->fields[2]->comment);
		$this->assertEquals("varchar(255)", $ent->fields[2]->type);

		$this->assertFalse($ent->fields[2]->isInherited);
		$this->assertTrue($ent->fields[2]->allowNull);
		$this->assertTrue($ent->fields[2]->isColomn);
		$this->assertFalse($ent->fields[2]->isAutoincremented);
	}

	public function testCreateFromTable()
	{
		$ent = EntityMetaManager::createFromTable("user");

		$this->assertEquals("user", $ent->name);
		$this->assertEquals("Таблица содержит информацию о типах пользователей Покупатель", $ent->comment);
		$this->assertEquals(8, count($ent->fields));

		$this->assertEquals("id", $ent->fields[0]->name);
		$this->assertEquals("Id сущности", $ent->fields[0]->comment);
		$this->assertEquals("int(11) unsigned", $ent->fields[0]->type);

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

	public function testSaveToFile()
	{
		$ent = EntityMetaManager::createFromTable("user");
		EntityMetaManager::saveToFile($ent, "fixtures/tmp/User.php");

		$this->assertEquals("7b1d9c615648f0ae63f980d7c6f68da3", md5_file("fixtures/tmp/User.php"));
	}

	public function testSaveToTable()
	{
		//clear temp table
		$this->db->exec("DROP TABLE IF EXISTS `_user`");

		$ent = EntityMetaManager::createFromFile("fixtures/entities/User.php");
		EntityMetaManager::saveToTable($ent, "_user");
	}

	public function testMergeAndSaveToTable()
	{
		$ent = EntityMetaManager::createFromFile("fixtures/entities/User.php");
		EntityMetaManager::mergeAndSaveToTable($ent, "user");
	}

}
