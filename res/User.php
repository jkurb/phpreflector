<?php
/**
 * Сущность пользователя
 *
 * PHP version 5
 *
 * @package
 * @author   Eugene Kurbatov <ekur@i-loto.ru>
 */

require_once 'Entity.php';

class User extends Entity
{
	/**
	 * Содержит наименование таблицы в БД
	 * 
	 * @var string
	 */
	public $entityTable = "user";
	
	/**
	 * Первичный ключ, обычно соответствует атрибуту "id".
	 * 
	 * @var string
	 */
	public $primaryKey = "id";

	/**
	 * Пароль пользователя
	 *
	 * @column(type="varchar(256)", unique=true, nullable=false)
	 * @var string
	 */
	public $password;

	/**
	 * Email пользователя
	 *
	 * @column(type="varchar(256)", unique=true, nullable=false)
	 * @var string
	 */
	public $email = null;

	/**
	 * Номер сотового телефона
	 *
	 * @column type="varchar(256)", nullable=false
	 * @var string
	 */
	public $cellPhone = null;

	/**
	 * Статус пользователя
	 *
	 * @column(type="varchar(64)", unique=true, nullable=false)
	 * @var string
	 */
	public $status = self::STATUS_UNVERIFIED;

	/**
	 * Дата регистрации
	 *
	 * @column(name="regDate", type="datetime", unique=true, nullable=false)
	 * @var DateTime
	 */
	public $registrationDate = null;


    /**
     * Represents hash like this:
     *
     * @return  array ('id' => ENTITY_FIELD_INT, 'name'=>ENTITY_FIELD_STRING)
     * */
    public function getFields()
    {
        // TODO: Implement getFields() method.
    }
}
?>