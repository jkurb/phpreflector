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
require_once "DbColomnParams.php";

require_once "Zend/Reflection/Class.php";
require_once "Zend/Reflection/File.php";
require_once "Zend/Config.php";
require_once "Zend/Db.php";

class ClassReflector implements IClassReflector
{
	private $classRef = null;

	const PHPDOC_TAG_COLUMN = "column";
    const INDENT = "\t";

	function __construct(Zend_Reflection_Class $classRef)
	{
		$this->classRef = $classRef;
	}

	public static function createFromFile($path, $classname)
	{
		require_once $path;
		return new ClassReflector(new Zend_Reflection_Class($classname));
	}

	public static function createFromTable($config, $tbname)
	{
        // table to class string
        $classStr = self::tableToClassString($config, $tbname);

        var_dump($classStr);

        exit();

        eval($classStr);

        return new ClassReflector(new Zend_Reflection_Class(ucfirst($tbname)));
	}

	public function reflectToTable($config, $tbname)
	{
		$conf = new Zend_Config($config);

		$db = Zend_Db::factory($conf->database);

		$db->getConnection();

		$sqlBegin = "CREATE TABLE {$tbname} (";
		$sql = "";

		$defaultProps = $this->classRef->getDefaultProperties();

		//todo: check id exist

		/** @var $p Zend_Reflection_Property */
		foreach ($this->classRef->getProperties() as $p)
		{
			if (!$p->getDocComment()->hasTag(self::PHPDOC_TAG_COLUMN))
				continue;

			$tagDesc = $p->getDocComment()->getTag(self::PHPDOC_TAG_COLUMN)->getDescription();
			$columnParams = DbColomnParams::parse($tagDesc);

			if ($columnParams->isId)
			{
				$columnParams->allowNull = false;
				$columnParams->isUnsigned = true;
				$columnParams->type = "int(11)";
				$columnParams->isAutoIncremented = true;
			}

 			$comment = trim($p->getDocComment()->getShortDescription());
			$val = $p->isStatic() ? $p->getValue($p) : $defaultProps[$p->getName()];
			$fieldName = isset($columnParams->name) ? $columnParams->name : $p->getName();
			$options = "";
			
			if ($columnParams->isUnsigned)
			{
				$options .= "UNSIGNED";
			}

			if ($columnParams->allowNull)
			{
				$options .= " DEFAULT NULL";
			}
			else
			{
				$options .= " NOT NULL";
				if (!is_null($val))
					$options .= " DEFAULT '{$val}'";
			}

			if ($columnParams->isAutoIncremented)
			{
				$options .= " AUTO_INCREMENT";
			}

			//id ever first
			if ($columnParams->isId)
			{
				$sql = "{$fieldName} {$columnParams->type} {$options} COMMENT '{$comment}', " . $sql;
			}
			else
			{
				$sql .= "{$fieldName} {$columnParams->type} {$options} COMMENT '{$comment}', ";
			}

		}
		$sqlEnd = "PRIMARY KEY (id)) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin;";

		echo $sqlBegin .$sql . $sqlEnd;

		$db->exec($sqlBegin . $sql . $sqlEnd);

	}

	public function reflectToFile($path, $classname)
	{
		$str = "<?php\n";
		$str .= $this->classRef->getDocComment() . "\n\n";
		$str .= $this->getStrClassDeclaration($classname);

		$str .= "\n{\n";

		$str .= $this->getStrProperties();
		$str .= $this->getStrConstants();
		$str .= $this->getStrMethods();

		$str .= "}";
		$str .= "\n?>";

		file_put_contents($path, $str);
		echo $str;
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

	private function getVal($val)
	{
		return is_null($val) ? "null" :
			(is_string($val) ? "\"$val\"" : $val);

	}


    private static function tableToClassString($config, $tblName)
    {
        $conf = new Zend_Config($config);
		$db = Zend_Db::factory($conf->database);
		$db->getConnection();

        $fieldsMeta = $db->fetchAssoc("SHOW FULL COLUMNS FROM {$tblName}");

        var_dump($fieldsMeta);
    }

	/*
	private static function recognizeDbType($phpType)
	{
		switch ($phpType)
		{
			case "int": case "integer": case "float": case "double":
				return array("INT", 11);
			case "string":
				return array("VARCHAR", 256);
			case "DateTime":
				return array("datetime", null);
		}
	}
	*/
}
