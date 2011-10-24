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
 *    type="int(11) unsigned",
 *
 *    { Может ли поле быть NULL }
 *    allowNull=true,
 *
 *    { Значение по умоляанию }
 *    default="null"
 *
 *    { Задается первичный ключ, автоинкрементный, безнаковый, int(11) }
 *    id
 */

class Field
{
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
     * Преобразует описание аннотации в объект класса
     *
     * @param $p Zend_Reflection_Property Поле класса
     * @param $defaultProps array Дефолтные значения полей класса
     * @return DbField
     */
	public static function extract($p, $defaultProps)
	{
		$arr = array();
		$res = explode(",", $p->getDocComment()->getTag("column")->getDescription());
		foreach ($res  as $r)
		{
			$pair = explode("=", $r);
			$name = trim($pair[0]);
			$value = isset($pair[1]) ? str_replace("\"", "", trim($pair[1])) : null;
			$arr[$name] = $value;
		}

		if (!isset($arr["type"]) && !key_exists("id", $arr))
			throw new RuntimeException("Type/ID is required");

		$field = new Field();

		if (isset($arr["name"]) && !empty($arr["name"]))
        {
            $field->name = $arr["name"];
        }
        else
        {
            $field->name = $p->getName();
        }

        //Задается первичный ключ, автоинкрементный, безнаковый, int(11)
        if (key_exists("id", $arr))
        {
            $field->isPrimaryKey = true;
            $field->isAutoincremented = true;
            $field->isId = true;
            $field->type = "int(11) insigned";
            $field->allowNull = false;
        }

		if (isset($arr["type"]) && !empty($arr["type"]))
        {
            $field->type = $arr["type"];
        }

		if (isset($arr["allowNull"]) && self::isBool($arr["allowNull"]))
        {
            $field->allowNull = self::getBool($arr["allowNull"]);
        }

        if (isset($arr["default"]) && !empty($arr["default"]))
        {
			$field->default = $arr["default"];
        }
        else
        {
            $val = $p->isStatic() ? $p->getValue($p) : $defaultProps[$p->getName()];
            $field->default = $val;
        }

        $field->comment = $p->getDocComment()->getShortDescription();

		return $field;
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
        $s = "@column ";



    }
}
