<?php
/**
 * Класс предназначен для отображения классов на таблицу БД и наоборот
 *
 * PHP version 5
 *
 * @package
 * @author   Eugene Kurbatov <ekur@i-loto.ru>
 */


require_once "IEntityMetaManager.php";
require_once "EntityMeta.php";

require_once "Zend/Reflection/Class.php";
require_once "Zend/Reflection/File.php";
require_once "Zend/Config.php";
require_once "Zend/Db.php";

use TokenReflection\ReflectionAnnotation;
use TokenReflection\Broker;
use TokenReflection\Broker\Backend;
use TokenReflection\Broker\Backend\Memory;

class EntityMetaManager implements IEntityMetaManager
{
	/**
	 * @var Zend_Db_Adapter_Pdo_Abstract
	 */
	static private $db = null;

	public static function init()
	{
		self::$db = Zend_Db::factory(Config::getInstance()->database);
		self::$db->getConnection();
	}

	/**
	 * Создание мета-сущности из файла
	 *
	 * @param string $path Путь к файлу класса
	 *
	 * @return EntityMeta
	 */
	public static function createFromFile($path)
	{
		$dirname = dirname($path);
		$classname = basename($path, ".php");

		$broker = new Broker(new Memory());
		$broker->processDirectory($dirname);

		$refClass = $broker->getClass($classname);

        $entityMeta = new EntityMeta();
        $entityMeta->name = strtolower($classname);

		$annotations = $refClass->getAnnotations();
        $entityMeta->comment = $annotations[ReflectionAnnotation::SHORT_DESCRIPTION];

		/** @var $c \TokenReflection\ReflectionConstant */
		foreach ($refClass->getConstantReflections() as $c)
		{
			$field = new Field();
			$field->name = $c->getName();
			$field->default = $c->getValue();
			$field->isConstant = true;
			$field->isInherited = $c->getDeclaringClassName() != $refClass->getName();

			$entityMeta->constants[] = $field;
		}

		/** @var $p \TokenReflection\ReflectionProperty */
		foreach ($refClass->getProperties() as $p)
		{
            $field = Field::extract($p);
			$field->isInherited = $p->getDeclaringClassName() != $refClass->getName();
            $entityMeta->fields[] = $field;
		}

		/** @var $m \TokenReflection\ReflectionMethod */
		foreach ($refClass->getOwnMethods() as $m)
		{
			$entityMeta->strMethods .= $m->getSource();
		}

        return $entityMeta;
	}

	/**
	 * Создание мета-сущности из таблицы
	 *
	 * @param string $tblName Имя таблицы
	 *
	 * @return EntityMeta
	 */
	public static function createFromTable($tblName)
	{
        $entityMeta = new EntityMeta();

        $tableMeta = self::$db->fetchAssoc("SHOW TABLE STATUS FROM " .
            Config::getInstance()->database->params->dbname . " WHERE Name = '{$tblName}'");

        $entityMeta->comment = $tableMeta[$tblName]["Comment"];
        $entityMeta->name = strtolower($tblName);

        $fieldsMeta =  self::$db->fetchAssoc("SHOW FULL COLUMNS FROM {$tblName}");

        foreach ($fieldsMeta as $f)
        {
            $field = new Field();

	        if ($f["Key"] == "PRI")
            {
                $field->isId = true;
                $field->isPrimaryKey = true;
            }

            $field->name = $f["Field"];
            $field->type = $f["Type"];
            $field->default = ($f["Default"] == "NULL") ? null : $f["Default"];
            $field->comment = $f["Comment"];
            $field->allowNull = ($f["Null"] == "YES");
            $field->isAutoincremented = ($f["Extra"] == "auto_increment");
	        $field->isColomn = true;
	        $field->isPublic = true;

            $entityMeta->fields[] = $field;
        }

        return $entityMeta;        
	}

	/**
	 * Сохранение сущности в таблицу БД
	 *
	 * @param EntityMeta $entityMeta Объект мета-сущности
	 * @param string $tbname  Имя таблицы
	 *
	 * @return void
	 */
	public static function saveToTable($entityMeta, $tbname)
	{
		$db = Zend_Db::factory(Config::getInstance()->database);
		$db->getConnection();

		$beginSql = "CREATE TABLE `$tbname` (";

		$sql = "";
		foreach ($entityMeta->fields as $field)
		{
			if (!$field->isColomn)
				continue;

			$defenition = $field->getColumnDefeniton();

			if ($field->isId)
			{
				$sql = "`{$field->name}` {$defenition}, " . $sql;
			}
			else
			{
				$sql .= "{$field->name} {$defenition}, ";
			}


		}
		$endSql = "PRIMARY KEY (id)) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci COMMENT='{$entityMeta->comment}';";

		$sql = "{$beginSql}{$sql}{$endSql}";

		self::$db->exec($sql);
	}

	/**
	 * Сохранение сущности в файл
	 *
	 * @param $entityMeta EntityMeta Объект мета-сущности
	 * @param string $path Путь до файла
	 *
	 * @return void
	 */
	public static function saveToFile($entityMeta, $path)
	{
		$classTplHandler = new ClassTemplateHandler(
			Config::getInstance()->classTemplate,
			Config::getInstance()->fieldTemplate,
			$entityMeta
		);

		$content = "<?php\n". $classTplHandler->process() . "\n?>";
		file_put_contents($path, $content);
	}

    /**
     * Сохрание сущности в файл со слиянием
     * todo: пока все перезаписывается из источника,
     * возможно стоит сделать режим soft, кода поля из приемника не удаляются (только добаляени и изменения)
     *
     * @param EntityMeta $entity Объект мета-сущности
     * @param string $path Путь до файла
     *
     * @return void
     */
	public static function mergeAndSaveToFile($entity, $path)
	{
		if (!is_file($path))
		{
			self::saveToFile($entity, $path);
		}
		else
		{
			self::saveToFile($entity, $path);

			/*
			//слияние
			$entityDest = self::createFromFile($path);

			$mergedEntity = $entity;

			foreach ($entityDest->fields as $field)
			{
				$f = $mergedEntity->findFieldByName($field->name);
				if ($f == null)
				{
					//вставляем поле в нужное место
				}
			}
			*/
		}
	}

	/**
	 * Сохрание таблицы со слиянием
	 *
	 * @param EntityMeta $entity Объект мета-сущности
	 * @param string $tblname Имя таблицы
	 *
	 * @return void
	 */
	public static function mergeAndSaveToTable($entity, $tblname)
	{
		if (!self::isTableExist($tblname))
		{
			self::saveToTable($entity, $tblname);
		}
		else
		{
			$entityDest = self::createFromTable($tblname);

			$sqlAlters = array();
			if ($entity->name != $entityDest->name)
			{
				$sqlAlters[] = "ALTER TABLE `{$tblname}` RENAME `{$entity->name}`";
			}

			if ($entity->comment != $entityDest->comment)
			{
				$sqlAlters[] = "ALTER TABLE `{$entity->name}` COMMENT='{$entity->comment}'";
			}

			foreach ($entity->fields as $field)
			{
				if (!$field->isColomn)
					continue;

				$defenition = $field->getColumnDefeniton();

				$fieldDest = $entityDest->findFieldByName($field->name);
				if ($fieldDest === null)
				{
					$sqlAlters[] = "ALTER TABLE `{$entity->name}` ADD COLUMN `{$field->name}` {$defenition}";
				}
				else if (self::isDifferentColumn($field, $fieldDest))
				{
					$sqlAlters[] = "ALTER TABLE `{$entity->name}` CHANGE COLUMN `{$field->name}` `{$field->name}` {$defenition}";
				}				
			}

			//удаление полей, которых нет в источник
			foreach ($entityDest->fields as $field)
			{
				if (!$field->isColomn)
					continue;

				if (!$entity->findFieldByName($field->name))
				{
					$sqlAlters[] = "ALTER TABLE `{$entity->name}` DROP COLUMN `{$field->name}`";
				}
			}

			foreach ($sqlAlters as $sql)
			{
				echo "{$sql}\n";
				self::$db->exec($sql);
			}
		}
	}

	/**
	 * @param $f1 Field
	 * @param $f2 Field
	 *
	 * @return mixed
	 */
	private static function isDifferentColumn($f1, $f2)
	{
		return ($f1->allowNull != $f2->allowNull)
			|| ($f1->isAutoincremented != $f2->isAutoincremented)
			|| ($f1->isPrimaryKey != $f2->isPrimaryKey)
			|| ($f1->isColomn != $f2->isColomn)
			|| ($f1->comment != $f2->comment)
			|| ($f1->default != $f2->default)
			|| ($f1->isId != $f2->isId)
			|| ($f1->name != $f2->name)
			|| ($f1->type != $f2->type);
	}

	private static function isTableExist($tblname)
	{
		try
		{
			self::$db->describeTable($tblname);
		}
		catch (Zend_Db_Adapter_Exception $e)
		{
			return false;
		}
		return true;
	}

}