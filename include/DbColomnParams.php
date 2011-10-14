<?php
/**
 * Представление параметров поля БД
 *
 * PHP version 5
 *
 * @package
 * @author   Eugene Kurbatov <ekur@i-loto.ru>
 */

class DbColomnParams
{
    /**
     * Имя поля
     *
     * @var string
     */
	public $name = null;

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
     * Является ли беззнаковым
     *
     * @var bool
     */
	public $isUnsigned = false;

    /**
     * Является ли id
     *
     * @var bool
     */
	public $isId = false;

    /**
     * Является ли автоинкрементным
     *
     * @var bool
     */
	public $isAutoIncremented = false;

    /**
     * Преобразует описание аннотации в объект класса
     *
     * @param $desc Описание поля
     * @return DbColomnParams
     */
	public static function parse($desc)
	{
		$arr = array();
		$res = explode(",", $desc);
		foreach ($res  as $r)
		{
			$pair = explode("=", $r);
			$name = trim($pair[0]);
			$value = isset($pair[1]) ? str_replace("\"", "", trim($pair[1])) : null;
			$arr[$name] = $value;
		}

		if (!isset($arr["type"]) && !key_exists("id", $arr))
			throw new RuntimeException("Type/ID is required");

		$p = new DbColomnParams();

		if (isset($arr["name"]) && !empty($arr["name"]))
			$p->name = $arr["name"];

		if (isset($arr["type"]) && !empty($arr["type"]))
			$p->type = $arr["type"];

		if (isset($arr["allowNull"]) && self::isBool($arr["allowNull"]))
			$p->allowNull = self::getBool($arr["allowNull"]);

		if (isset($arr["unsigned"]) && self::isBool($arr["unsigned"]))
			$p->isUnsigned =  self::getBool($arr["unsigned"]);

		if (isset($arr["autoincrement"]) && self::isBool($arr["autoincrement"]))
			$p->isAutoIncremented =  self::getBool($arr["autoincrement"]);

		if (key_exists("id", $arr))
			$p->isId = true;

		return $p;
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
