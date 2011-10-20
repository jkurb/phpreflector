<?php
/**
 * Класс предназначен для отображения классов на таблицу БД и наоборот
 *
 * PHP version 5
 *
 * @package
 * @author   Eugene Kurbatov <ekur@i-loto.ru>
 */

require_once "IClassReflector.php";
require_once "EntityMeta.php";

require_once "Zend/Reflection/Class.php";
require_once "Zend/Reflection/File.php";
require_once "Zend/Config.php";
require_once "Zend/Db.php";

class EntityMetaManager implements IEntityMetaManager
{
	const PHPDOC_TAG_COLUMN = "column";
    const INDENT = "\t";

	public static function createFromFile($path, $classname)
	{
		require_once $path;
        $refClass = new Zend_Reflection_Class($classname);

        $entityMeta = new EntityMeta();
        $entityMeta->name = $classname;
        $entityMeta->comment = $refClass->getDocblock()->getShortDescription();

		/** @var $p Zend_Reflection_Property */
		foreach ($refClass->getProperties() as $p)
		{
            if (!$p->getDocComment()->hasTag(self::PHPDOC_TAG_COLUMN))
                continue;

            $field = Field::extract($p, $refClass->getDefaultProperties());
            $entityMeta->fields[] = $field;
		}

        return $entityMeta;
	}

	public static function createFromTable($config, $tblName)
	{
        $entityMeta = new EntityMeta();

        $conf = new Zend_Config($config);
		$db = Zend_Db::factory($conf->database);
		$db->getConnection();

        $tableMeta = $db->fetchAssoc("SHOW TABLE STATUS FROM
            {$conf->database->params->dbname} WHERE Name = '{$tblName}'");

        $entityMeta->comment = $tableMeta[$tblName]["Comment"];
        $entityMeta->name = ucfirst($tblName);

        $fieldsMeta = $db->fetchAssoc("SHOW FULL COLUMNS FROM {$tblName}");        ;

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

            $entityMeta->fields[] = $field;
        }

        return $entityMeta;        
	}

	public static function saveToTable($config, $tbname)
	{

//		$conf = new Zend_Config($config);
//
//		$db = Zend_Db::factory($conf->database);
//
//		$db->getConnection();
//
//		$sqlBegin = "CREATE TABLE {$tbname} (";
//		$sql = "";
//
//		$defaultProps = $this->classRef->getDefaultProperties();
//
//		//todo: check id exist
//
//		/** @var $p Zend_Reflection_Property */
//		foreach ($this->classRef->getProperties() as $p)
//		{
//			if (!$p->getDocComment()->hasTag(self::PHPDOC_TAG_COLUMN))
//				continue;
//
//			$tagDesc = $p->getDocComment()->getTag(self::PHPDOC_TAG_COLUMN)->getDescription();
//			$columnParams = Field::extract($tagDesc);
//
//			if ($columnParams->isId)
//			{
//				$columnParams->allowNull = false;
//				$columnParams->isUnsigned = true;
//				$columnParams->type = "int(11)";
//				$columnParams->isAutoIncremented = true;
//			}
//
// 			$comment = trim($p->getDocComment()->getShortDescription());
//			$val = $p->isStatic() ? $p->getValue($p) : $defaultProps[$p->getName()];
//			$fieldName = isset($columnParams->name) ? $columnParams->name : $p->getName();
//			$options = "";
//
//			if ($columnParams->isUnsigned)
//			{
//				$options .= "UNSIGNED";
//			}
//
//			if ($columnParams->allowNull)
//			{
//				$options .= " DEFAULT NULL";
//			}
//			else
//			{
//				$options .= " NOT NULL";
//				if (!is_null($val))
//					$options .= " DEFAULT '{$val}'";
//			}
//
//			if ($columnParams->isAutoIncremented)
//			{
//				$options .= " AUTO_INCREMENT";
//			}
//
//			//id ever first
//			if ($columnParams->isId)
//			{
//				$sql = "{$fieldName} {$columnParams->type} {$options} COMMENT '{$comment}', " . $sql;
//			}
//			else
//			{
//				$sql .= "{$fieldName} {$columnParams->type} {$options} COMMENT '{$comment}', ";
//			}
//
//		}
//		$sqlEnd = "PRIMARY KEY (id)) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin;";
//
//		echo $sqlBegin .$sql . $sqlEnd;
//
//		$db->exec($sqlBegin . $sql . $sqlEnd);

	}

	public static function saveToFile($path, $classname)
	{
//		$str = "<?php\n";
//		$str .= $this->classRef->getDocComment() . "\n\n";
//		$str .= $this->getStrClassDeclaration($classname);
//
//		$str .= "\n{\n";
//
//		$str .= $this->getStrProperties();
//		$str .= $this->getStrConstants();
//		$str .= $this->getStrMethods();
//
//		$str .= "}";
//		$str .= "\n>";
//
//		file_put_contents($path, $str);
//		echo $str;
	}




    
	private function getStrClassDeclaration($classname)
	{
		$str = "";
		foreach (Reflection::getModifierNames($this->classRef->getModifiers()) as $m)
			$str .= "$m ";

		$str .= "class " . $classname;

		$parentClass = $this->classRef->getParentClass();

		if ($parentClass) {
			$str .= " extends " . $parentClass->getName();
		}

		$interfaces = $this->classRef->getInterfaces();

		if (count($interfaces) > 0)
		{
			$str .= " implements ";
			$i = 0;
			/** @var $int Zend_Reflection_Class */
			foreach ($interfaces as $int)
			{
				$i++;
				$delim = ", ";
				if ($i == count($interfaces))
					$delim = "";

				$str .= $int->getName() . $delim;
			}

		}
		return $str;
	}

	private function getStrProperties()
	{
		$str = "";
		$defaultProps = $this->classRef->getDefaultProperties();

		/** @var $p Zend_Reflection_Property */
		foreach ($this->classRef->getProperties() as $p)
		{
			$str .= self::INDENT;
			$str .= $p->getDocComment()->getContents() . "\n";
			$str .= self::INDENT;

			foreach (Reflection::getModifierNames($p->getModifiers()) as $m)
				$str .= "$m ";

			$val = $p->isStatic() ? $p->getValue($p) : $defaultProps[$p->getName()];

			$str .= "$" . $p->getName() . " = " . $this->getVal($val) . ";";

			$str .= "\n\n";
		}

		return $str;
	}

	private function getStrConstants()
	{
		$str = "";
		$parentClass = $this->classRef->getParentClass();
		$parentConsts = array();
		if ($parentClass)
		{
			$parentConsts = $parentClass->getConstants();
		}

		foreach ($this->classRef->getConstants() as $cName => $cVal)
		{
			if (!key_exists($cName, $parentConsts))
			{
				$str .= self::INDENT . "const ";
				$str .= $cName . " = " . $this->getVal($cVal) . ";";
				$str .= "\n\n";
			}
		}

		return $str;
	}

	private function getStrMethods()
	{
		$str = "";
		/** @var $m Zend_Reflection_Method */
		foreach ($this->classRef->getMethods() as $m)
		{
			if ($m->getDeclaringClass()->getName() == "User")
			{
				$str .= self::INDENT;
				$str .= $m->getDocComment() . "\n";
				$str .= self::INDENT;

				foreach (Reflection::getModifierNames($m->getModifiers()) as $mod)
					$str .= "$mod ";

				$str .= "function " . $m->getName();
				$str .= "(";

				/** @var $p Zend_Reflection_Parameter */
				$i = 0;
				foreach ($m->getParameters() as $p)
				{
					$i++;
					$delim = ", ";
					if ($i == count($m->getParameters()))
						$delim = "";

					$str .= "$" . $p->getName() . $delim .
						($p->isOptional() ? " = " . $this->getVal($p->getDefaultValue()) : "");
				}

				$str .= ")";

				$str .= "\n" . self::INDENT . "{\n";
				$str .= $m->getBody();
				$str .= "\n" . self::INDENT . "}";

				$str .= "\n\n";
			}
		}

		return $str;
	}

	private static function getVal($val)
	{
		return is_null($val) ? "null" :
            (is_numeric($val) ? $val :
			    (is_string($val) ? "\"$val\"" : $val)
            );

	}

    private static function tableToClassString($config, $tblName)
    {
        $conf = new Zend_Config($config);
		$db = Zend_Db::factory($conf->database);
		$db->getConnection();
       
        $tableMeta = $db->fetchAssoc("SHOW TABLE STATUS FROM
            {$conf->database->params->dbname} WHERE Name = '{$tblName}'");

        $fieldsMeta = $db->fetchAssoc("SHOW FULL COLUMNS FROM {$tblName}");

        var_dump($fieldsMeta);

        $fieldsStr = "";
        $primaryKey = "id";

        foreach ($fieldsMeta as $field)
        {
	        if ($field["Key"] == "PRI")
		        $primaryKey = $field["Field"];

	        if ($field["Key"] !== "PRI")
	        {
                $type = preg_replace('/\(.*\)/', "", $field["Type"]);
                $replaceMapFileld =  array
                (
                    "{COMMENT}"           => $field["Comment"],
                    "{TYPE}"              => self::recognizeDbType($type),
                    "{COLUMN_ANNOTATION}" => self::PHPDOC_TAG_COLUMN . ".....",
                    "{COLUMN_NAME}"       => $field["Field"],
                    "{DEFAULT_VALUE}"     => self::getVal($field["Default"])
                );

                $fieldsStr .= str_replace(
                    array_keys($replaceMapFileld),
                    array_values($replaceMapFileld),
                    file_get_contents($conf->get("fieldTemplate"))
                );
        	}
        }

        $replaceMapClass =  array
        (
            "{TABLE_COMMENT}"    => $tableMeta[$tblName]["Comment"],
            "{AUTHOR}"           => "Eugene Kurbatov",
            "{ENTITY_NAME}"      => ucfirst($tblName),
            "{ENTITY_TABLE}"     => $tblName,
            "{PRIMARY_KEY}"      => $primaryKey,
            "{FIELDS_META_DATA}" => $fieldsStr,
            "{FIELDS_LIST}"      => "//fields"
        );

        $classStr = str_replace(
            array_keys($replaceMapClass),
            array_values($replaceMapClass),
            file_get_contents($conf->get("entityTemplate"))
        );
        return $classStr;
    }


	private static function recognizeDbType($dbType)
	{
        switch ($dbType)
        {
            case "int": case "tinyint": case "bit":
                return "integer";
                break;
            case "float":
                return "float";
                break;
            case "double":
                return "double";
                break;
            case "decimal":
                return "decimal";
                break;
            case "date": case "timestamp": case "datetime":
                return "DateTime";
                break;
            case "varchar": case "text": case "tinytext": case "char":
                return "string";
                break;
            default:
                throw new Exception("Undefined type '{$dbType}'") ;
        }
	}

    private static function getColumnAnnotation($type, $null, $default)
    {
        //Type
        //Null
        //Default
        //Extra
    }

}
