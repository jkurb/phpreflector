<?php
/**
 * Обработчик шаблона класса
 *
 * PHP version 5
 *
 * @package
 * @author  Eugene Kurbatov <ekur@i-loto.ru>
 */

class ClassTemplateHandler extends BaseTemplateHandler
{
	/**
	 * @var EntityMeta
	 */
	protected $entity = null;

	/**
	 * Объект обработчика поля класса
	 *
	 * @var FieldTemplateHandler
	 */
	protected $fieldTemplateHandler = null;

	public function __construct($entity)
	{
		$this->entity = $entity;
		$this->fieldTemplateHandler = new FieldTemplateHandler();
	}

	public function getEntityComment()
	{
		return $this->entity->comment;
	}

	public function getEntityName()
	{
		return ucfirst($this->entity->name);
	}

	public function getEntityMethods()
	{
		$ignoredMethods = array("getFields");

		$methodsStr = "";

		/** @var $m \TokenReflection\ReflectionMethod */
		foreach ($this->entity->methods as $m)
		{
			if (in_array($m->getName(), $ignoredMethods))
				continue;

			$methodsStr .= "\n\t" . $m->getSource() . "\n";
		}

		return $methodsStr;
	}

	public function getEntityConstants()
	{
		$constantsStr = "";
		foreach ($this->entity->constants as $c)
		{
			$tplFile = Config::getInstance()->templatesDir . "/field.const.tpl";
			$constantsStr .= $this->fieldTemplateHandler->process($c, $tplFile);
		}
		return $constantsStr;
	}

	public function getEntityFields()
	{
		$ignoredFields = array("id", "entityTable", "primaryKey");

		$fieldsStr = "";
		foreach ($this->entity->fields as $f)
		{
			if (in_array($f->name, $ignoredFields))
				continue;

			if ($f->isColomn)
			{
				$tplFile = Config::getInstance()->templatesDir . "/field.column.tpl";
			}
			else
			{
				$tplFile = Config::getInstance()->templatesDir . "/field.tpl";
			}

			$fieldsStr .= $this->fieldTemplateHandler->process($f, $tplFile);
		}

		return $fieldsStr;
	}

	public function getCustomFieldsList()
	{
		$str = "";
		foreach ($this->entity->fields as $f)
		{
			if (!$f->isColomn)
				continue;

			$str .= "\t\t\t\"{$f->name}\" => " . $this->recognizeSoloEntityFieldType($f->type) . ",\n";
		}

		return $str;
	}

	public function getCustomPrimaryKey()
	{
		return "id";
	}

	public function getCustomEntityTable()
	{
		return strtolower($this->entity->name);
	}

	public function getCustomAuthor()
	{
		return Config::getInstance()->author;
	}

	private function recognizeSoloEntityFieldType($dbType)
	{
		$typeName = preg_replace("/\\W.*/", "", $dbType);

		switch ($typeName)
	    {
	        case "int":	case "tinyint": case "bit": case "float":
	            return "self::ENTITY_FIELD_INT";
	        case "decimal":
		        return "self::ENTITY_FIELD_DECIMAL";
	        case "char": case "varchar": case "text": case "tinytext": case "mediumtext":
				return "self::ENTITY_FIELD_STRING";
	        case "date": case "timestamp": case "datetime":
				return "self::ENTITY_FIELD_DATETIME";
	        default:
	            throw new Exception("Undefined type '{$typeName}'") ;
	    }
	}
}
