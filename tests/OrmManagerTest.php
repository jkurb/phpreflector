<?php
require_once "../bootstrap.php";

class OrmManagerTest extends PHPUnit_Framework_TestCase
{
	/**
	 * @var PDO
	 */
    static private $db = null;

	/**
	 * Returns the test database connection.
	 *
	 * @return PDO
	 */
    final public function getConnection()
    {
        if (self::$db == null)
        {
            self::$db = new PDO("mysql:dbname=test_ormmanager;host=localhost", "root", "toor",
	            array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
        }
        return self::$db;
    }

	public function __construct()
	{
		OrmManager::init("data/config.php");
	}

	public function setUp()
	{
		$this->dropUserTable();
	}

	public function testCreateFromFile()
	{
		$ent = OrmManager::createFromFile("data/classes/User.php");

		$this->assertEquals("user", $ent->name);
		$this->assertEquals("Таблица содержит информацию о типах пользователей Покупатель", $ent->comment);
		$this->assertEquals(9, count($ent->fields));
		$this->assertEquals(8, count($ent->constants));
		$this->assertEquals("d46125e15f318e34f3b10ca73e71db68", md5($ent->methods));

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
		$this->assertFalse($ent->fields[2]->allowNull);
		$this->assertTrue($ent->fields[2]->isColomn);
		$this->assertFalse($ent->fields[2]->isAutoincremented);
	}


	public function testCreateFromTable()
	{
		$this->restoreUserTable();

		$ent = OrmManager::createFromTable("user");

		$this->assertEquals("user", $ent->name);
		$this->assertEquals("Таблица содержит информацию о типах пользователей Покупатель", $ent->comment);
		$this->assertEquals(7, count($ent->fields));

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
		$this->assertFalse($ent->fields[1]->allowNull);
		$this->assertTrue($ent->fields[1]->isColomn);
		$this->assertTrue($ent->fields[1]->isPublic);
		$this->assertFalse($ent->fields[1]->isPrimaryKey);
		$this->assertFalse($ent->fields[1]->isAutoincremented);

		$this->assertNull($ent->methods);
	}

	public function testSaveToFile()
	{
		$this->restoreUserTable();

		$ent = OrmManager::createFromTable("user");
		OrmManager::saveToFile($ent, "data/tmp/User.php");

		$this->assertEquals("4f957a31b5ee8e51d75a47bbba562a68", md5_file("data/tmp/User.php"));
	}

	public function testSaveToTable()
	{
		$dataSet = new PHPUnit_Extensions_Database_DataSet_XmlDataSet("data/schemes/userMeta.xml");
		$userMetaExpected = $dataSet->getTable("userMeta");

		$ent = OrmManager::createFromFile("data/classes/User.php");
		OrmManager::saveToTable($ent, "user");

		$userMetaActual = $this->getUserMeta();
		$userMetaExpected->assertEquals($userMetaActual);
	}

	public function testMergeAndSaveToFile()
	{
		$this->restoreUserTable();

		$ent = OrmManager::createFromTable("user");

		OrmManager::mergeAndSaveToFile($ent, "data/tmp/User.php");

		$this->assertEquals("4f957a31b5ee8e51d75a47bbba562a68", md5_file("data/tmp/User.php"));
	}

	public function testMergeAndSaveToTable()
	{
		$dataSet = new PHPUnit_Extensions_Database_DataSet_XmlDataSet("data/schemes/userMeta.xml");
		$userMetaExpected = $dataSet->getTable("userMeta");

		$ent = OrmManager::createFromFile("data/classes/User.php");

		$this->restoreUserTable();

		OrmManager::mergeAndSaveToTable($ent, "user");

		$userMetaActual = $this->getUserMeta();
		$userMetaExpected->assertEquals($userMetaActual);
	}

	private function restoreUserTable()
	{
		$this->getConnection()->exec(file_get_contents("data/schemes/user.sql"));
	}

	private function dropUserTable()
	{
		$this->getConnection()->exec("DROP TABLE IF EXISTS `user`");
	}

	/**
	 * @return PHPUnit_Extensions_Database_DB_Table
	 */
	private function getUserMeta()
	{
		$conn = new PHPUnit_Extensions_Database_DB_DefaultDatabaseConnection($this->getConnection(), "test_ormmanager");
		return $conn->createQueryTable("userMeta", "SHOW FULL COLUMNS FROM `user`");
	}
}
