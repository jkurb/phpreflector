<?php
/**
 * Метаданные поля сущности
 *
 * PHP version 5
 *
 * @package
 * @author   Eugene Kurbatov <ekur@i-loto.ru>
 */

/** @column
 *    { Название поля; если не задано, соответствует атрибуту класса }
 *    name="fileld"
 *
 *    { Тип поля; обязательное, если не задано id }
 *    type="int(11) unsigned"
 *
 *    { Может ли поле быть NULL }
 *    allowNull="true"
 *
 *    { Значение по умолчанию }
 *    default="null"
 *
 *    { Задается первичный ключ, автоинкрементный, безнаковый, int }
 *    id
 */

use TokenReflection\ReflectionAnnotation;

class Field
{
	const PHPDOC_TAG_COLUMN = "column";

	const ANNOTATE_NAME = "name";

	const ANNOTATE_ID = "id";

	const ANNOTATE_TYPE = "type";

	const ANNOTATE_ALLOW_NULL = "allowNull";

	const ANNOTATE_DEFAULT = "default";

    /**
     * Имя поля
     *
     * @var string
     */
	public $name = null;

    /**
     * Комментарий
     *
     * @var string
     */
    public $comment = null;

    /**
     * Тип поля
     *
     * @var string
     */
	public $type = null;

    /**
     * Возможно ли значение null
     *
     * @var bool
     */
	public $allowNull = true;

    /**
     * Значение по умолчанию
     *
     * @var mixed
     */
    public $default = null;

    /**
     * Является ли id
     *
     * @var bool
     */
    public $isId = false;

    /**
     * Является ли первичным ключом
     *
     * @var bool
     */
    public $isPrimaryKey = false;

    /**
     * Является ли автоинкрементым
     *
     * @var bool;
     */
    public $isAutoincremented = false;

	/**
	 * Является ли статик
	 *
	 * @var bool
	 */
	public $isStatic = false;

	/**
	 * Является ли публичным
	 *
	 * @var bool
	 */
	public $isPublic = false;

	/**
	 * Является ли приватным
	 *
	 * @var bool
	 */
	public $isPrivate = false;

	/**
	 * Является ли защищенным
	 *
	 * @var bool
	 */
	public $isProtected = false;

	/**
	 * Является ли полем БД
	 *
	 * @var bool
	 */
	public $isColomn = false;

	/**
	 * Является ли константой
	 *
	 * @var bool
	 */
	public $isConstant = false;

	/**
	 * Является ли унаследованным
	 *
	 * @var bool
	 */
	public $isInherited = false;

    /**
     * Создает объект Field c необходимыми полями из ReflectionProperty
     *
     * @param $p \TokenReflection\ReflectionProperty
     * @return Field
     */
	public static function extract($p)
	{
		$field = new Field();
		$arrAnnotation = array();

		if ($p->hasAnnotation(self::PHPDOC_TAG_COLUMN))
		{
			$arrAnnotation = self::readColomnAnnotation($p);
			$field->isColomn = true;
		}

		//Название поля
		if (isset($arrAnnotation[self::ANNOTATE_NAME]) &&
			!empty($arrAnnotation[self::ANNOTATE_NAME]))
        {
            $field->name = $arrAnnotation[self::ANNOTATE_NAME];
        }
        else
        {
            $field->name = $p->getName();
        }

        //Задается первичный ключ, автоинкрементный, беззнаковый, int
        if (key_exists(self::ANNOTATE_ID, $arrAnnotation))
        {
            $field->isPrimaryKey = true;
            $field->isAutoincremented = true;
            $field->isId = true;
            $field->type = Config::getInstance()->defaultIdType;
            $field->allowNull = false;
        }

		//Тип данных; переопределяем
		if (isset($arrAnnotation[self::ANNOTATE_TYPE]) &&
			!empty($arrAnnotation[self::ANNOTATE_TYPE]))
        {
            $field->type = $arrAnnotation[self::ANNOTATE_TYPE];
        }

		if (isset($arrAnnotation[self::ANNOTATE_ALLOW_NULL]) &&
			self::isBool($arrAnnotation[self::ANNOTATE_ALLOW_NULL]))
        {
            $field->allowNull = self::getBool($arrAnnotation[self::ANNOTATE_ALLOW_NULL]);
        }

		//Значение по умолчанию
        if (isset($arrAnnotation[self::ANNOTATE_DEFAULT]) &&
		    !empty($arrAnnotation[self::ANNOTATE_DEFAULT]))
        {
	        $field->default = $arrAnnotation[self::ANNOTATE_DEFAULT];
        }
        else
        {
	        $defaultProps = $p->getDeclaringClass()->getDefaultProperties();
            $val = $p->isStatic() ? $p->getValue($p) : $defaultProps[$p->getName()];
            $field->default = $val;
        }

		/*
		if (substr($p->getDefaultValueDefinition(), 0, strlen("self::")) == "self::")
        {
	        $field->default = $p->getDefaultValueDefinition();
        }
		*/

        $field->comment = $p->getAnnotation(ReflectionAnnotation::SHORT_DESCRIPTION);

		$field->isPrivate = $p->isPrivate();
		$field->isPublic = $p->isPublic();
		$field->isProtected = $p->isProtected();
		$field->isStatic = $p->isStatic();

		return $field;
	}

	/**
	 * Возвращает асоциативный массив; ключи соответсвуют полям класса
	 *
	 * @static
	 * @param $p \TokenReflection\ReflectionProperty
	 * @return array
	 */
	private static function readColomnAnnotation($p)
	{
		$arrAnnotations = array();
		$ann = $p->getAnnotation(self::PHPDOC_TAG_COLUMN);
		$res = explode(",", $ann[0]);
		foreach ($res as $r)
		{
			$pairKeyVal = explode("=", $r);
			$name = trim($pairKeyVal[0]);
			$value = isset($pairKeyVal[1]) ? str_replace("\"", "", trim($pairKeyVal[1])) : null;
			$arrAnnotations[$name] = $value;
		}
		return $arrAnnotations;
	}

	/**
	 * Возвращает phpdoc аннотацию поля БД
	 *
	 * @return string
	 */
	public function getColumnAnnotation()
	{
		$annotation = "@" . self::PHPDOC_TAG_COLUMN;

		if ($this->isId)
		{
			$annotation .= " " . self::ANNOTATE_ID;
		}
		else
		{
			$annotation .= " " . self::ANNOTATE_TYPE . "=\"{$this->type}\", "
				. self::ANNOTATE_ALLOW_NULL . "=\""
				. ($this->allowNull ? "true" : "false") . "\"";

			if ($this->default)
				$annotation .= ", " . self::ANNOTATE_DEFAULT . "=\"{$this->default}\"";
		}

		return $annotation;
	}

	/**
	 * Возвращает строку-определение поля БД
	 *
	 * @return string
	 */
	public function getColumnDefeniton()
	{
		$defenition = strtoupper($this->type);
		if ($this->allowNull)
		{
			$defenition .= " DEFAULT NULL";
		}
		else
		{
			$defenition .= " NOT NULL";
			if (!is_null($this->default))
			{
				$defenition .= " DEFAULT ";

				if ($this->default == "CURRENT_TIMESTAMP")
				{
					$defenition .= "{$this->default}";
				}
				else
				{
					$defenition .= "'{$this->default}'";
				}
			}
		}

		if ($this->isAutoincremented)
		{
			$defenition .= " AUTO_INCREMENT";
		}

		$defenition .= " COMMENT '{$this->comment}'";

		return $defenition;
	}

	private static function isBool($val)
	{
		return $val == "true" || $val == "false";
	}

	private static function getBool($str)
	{
		return $str == "true";
	}

}
