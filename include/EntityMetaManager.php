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
    const INDENT = "\t";

	/**
	 * Создание мета-сущности из файла
	 *
	 * @param $path string Путь к файлу класса
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
	 * @param $tblName string Имя таблицы
	 *
	 * @return EntityMeta
	 */
	public static function createFromTable($tblName)
	{
        $entityMeta = new EntityMeta();

		$db = Zend_Db::factory(Config::getInstance()->database);
		$db->getConnection();

        $tableMeta = $db->fetchAssoc("SHOW TABLE STATUS FROM " .
            Config::getInstance()->database->params->dbname . " WHERE Name = '{$tblName}'");

        $entityMeta->comment = $tableMeta[$tblName]["Comment"];
        $entityMeta->name = strtolower($tblName);

        $fieldsMeta = $db->fetchAssoc("SHOW FULL COLUMNS FROM {$tblName}");

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
	 * @param $entityMeta EntityMeta Объект мета-сущности
	 * @param $tbname string Имя таблицы
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

			$options = "";
			if ($field->allowNull)
			{
				$options .= " DEFAULT NULL";
			}
			else
			{
				$options .= " NOT NULL";
				if (!is_null($field->default))
				{
					$options .= " DEFAULT ";

					if ($field->default == "CURRENT_TIMESTAMP")
					{
						$options .= "`{$field->default}`";
					}
					else
					{
						$options .= "'{$field->default}'";
					}
				}
			}

			if ($field->isAutoincremented)
			{
				$options .= " AUTO_INCREMENT";
			}

			if ($field->isId)
			{
				$sql = "`{$field->name}` {$field->type} {$options} COMMENT '{$field->comment}', " . $sql;
			}
			else
			{
				$sql .= "{$field->name} {$field->type} {$options} COMMENT '{$field->comment}', ";
			}


		}
		$endSql = "PRIMARY KEY (id)) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci COMMENT='{$entityMeta->comment}';";

		$sql = "{$beginSql}{$sql}{$endSql}";

		$db->exec($sql);
	}

	/**
	 * Сохранение сущности в файл
	 *
	 * @param $entityMeta EntityMeta Объект мета-сущности
	 * @param $path Путь до файла
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
     *
     * @param EntityMeta $entity
     * @param string $path
     * @return void
     */
	public static function mergeAndSaveToFile($entity, $path)
	{
        //файл найден, выполним слияние
        if (is_file($path))
        {
            require_once $path;
            $classRef = new Zend_Reflection_Class($entity->name);

            $str = "<?php\n";

            $str .= $entity->comment . "\n\n";
		    $str .= self::getStrClassDeclaration($classRef);

		    $str .= "\n{\n";

		    $defaultProps = $classRef->getDefaultProperties();

            //пробежимся по полям сущности
            /** @var $p Zend_Reflection_Property */
            foreach ($classRef->getProperties() as $p)
            {
	            //пропускаем унаследованные
	            if ($p->getDeclaringClass()->getName() != $classRef->getName())
		            continue;

	            // поле исходной сущности
                $fieldSource = self::getFieldByName($entity, $p->getName());

                //найдены поля с одинаковыми названиями, сравниваем, показываем разницу, берем из источника
                if (!is_null($fieldSource))
                {
                    // поле сущности из файла
                    $fieldDest = Field::extract($p, $defaultProps);

                    //пробежим по атрибутам поля источника
                    foreach ($fieldSource as $fieldAtribName => $fieldAtribVal)
                    {
                        //если значение атрибутов различно, пишем значение атрибута на основе шаблона
                        if ($fieldAtribVal != $fieldDest->$fieldAtribName)
                        {
                            echo "Field: {$p->getName()}\n";
                            echo "Atrrib: {$fieldAtribName}\n";
                            echo "Source val: {$fieldAtribVal}\n";
                            echo "Dest val: {$fieldDest->$fieldAtribName}\n\n\n";
                        }
                    }

	                $type = preg_replace('/\(.*\).*/', "", $fieldSource->type);
					$replaceMapFileld = array(
						"{COMMENT}"           => $fieldSource->comment,
						"{TYPE}"              => self::recognizeDbType($type),
						"{COLUMN_ANNOTATION}" => self::PHPDOC_TAG_COLUMN . ".....",
						"{COLUMN_NAME}"       => $fieldSource->name,
						"{DEFAULT_VALUE}"     => self::getVal($fieldSource->default)
					);

	                $str .= str_replace(
						array_keys($replaceMapFileld),
						array_values($replaceMapFileld),
						file_get_contents(self::$config->get("fieldTemplate"))
					);
                }
                else
                {

                    $str .= self::INDENT;
                    $str .= $p->getDocComment()->getContents() . "\n";
                    $str .= self::INDENT;

                    foreach (Reflection::getModifierNames($p->getModifiers()) as $m)
                        $str .= "$m ";

                    $val = $p->isStatic() ? $p->getValue($p) : $defaultProps[$p->getName()];
                    $str .= "$" . $p->getName() . " = " . self::getVal($val) . ";";
                    $str .= "\n\n";
                }
            }

	        //todo: добавить поля сущности которых нет в файле

		    $str .= self::getStrConstants($classRef);
		    $str .= self::getStrMethods($classRef);

		    $str .= "}";
		    $str .= "\n?>";

		    //file_put_contents($path, $str);
        }
		else
		{
			$str = self::createClassFileByTemplate($entity);
		}

		//echo iconv("utf-8", "windows-1251", $str);
		echo "$str\n";



	}

	public static function merge($srcEntityMeta, $destEntityMeta)
	{
		// TODO: Implement merge() method.
	}
}
