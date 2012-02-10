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
 *    { Задается первичный ключ, автоинкрементный, безнаковый, int(11) }
 *    id
 */

class Field
{
	const PHPDOC_TAG_COLUMN = "column";

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
		if (isset($arrAnnotation["name"]) && !empty($arrAnnotation["name"]))
        {
            $field->name = $arrAnnotation["name"];
        }
        else
        {
            $field->name = $p->getName();
        }

        //Задается первичный ключ, автоинкрементный, беззнаковый, int(11)
        if (key_exists("id", $arrAnnotation))
        {
            $field->isPrimaryKey = true;
            $field->isAutoincremented = true;
            $field->isId = true;
	        // по умолчанию
            $field->type = "int(11) unsigned";
            $field->allowNull = false;
        }

		//Тип данных; переопределяем
		if (isset($arrAnnotation["type"]) && !empty($arrAnnotation["type"]))
        {
            $field->type = $arrAnnotation["type"];
        }

		if (isset($arrAnnotation["allowNull"]) && self::isBool($arrAnnotation["allowNull"]))
        {
            $field->allowNull = self::getBool($arrAnnotation["allowNull"]);
        }

		//Значение по умолчанию
        if (isset($arrAnnotation["default"]) && !empty($arrAnnotation["default"]))
        {
			$field->default = $arrAnnotation["default"];
        }
        else
        {
	        $defaultProps = $p->getDeclaringClass()->getDefaultProperties();
            $val = $p->isStatic() ? $p->getValue($p) : $defaultProps[$p->getName()];
            $field->default = $val;
        }

        $field->comment = $p->getAnnotation(\TokenReflection\ReflectionAnnotation::SHORT_DESCRIPTION);

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

	private static function isBool($val)
	{
		return $val == "true" || $val == "false";
	}

	private static function getBool($str)
	{
		return $str == "true";
	}

	public function getColumnAnnotation()
	{
		$annotation = "@" . self::PHPDOC_TAG_COLUMN;

		if ($this->isId)
		{
			$annotation .= " id";
		}
		else
		{
			$annotation .= " type=\"{$this->type}\", allowNull=\""
				. ($this->allowNull ? "true" : "false") . "\"";

			if ($this->default)
				$annotation .= ", default=\"{$this->default}\"";
		}

		return $annotation;
	}

}
