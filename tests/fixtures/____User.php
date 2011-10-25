<?php
/**
 * Сущность пользователя
 *
 * PHP version 5
 *
 * @package Tools
 * @author  Eugene Kurbatov <ekur@i-loto.ru>
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
     * @var string
	 * @column type="varchar(255)", allowNull=false
	 */
	public $password;

	/**
	 * Email пользователя
	 *
     * @var string
	 * @column type="varchar(255)", allowNull=false
	 */
	public $email = null;

	/**
	 * Номер сотового телефона
	 *
     * @var string
	 * @column type="varchar(255)", allowNull=false
	 */
	public $cellPhone = null;

	/**
	 * Статус пользователя
	 *
     * @var string
	 * @column type="int(1)", allowNull=false
	 */
	public $status = 1;

	/**
	 * Дата регистрации
	 *
     * @var DateTime
	 * @column name="registrationDate", type="timestamp", allowNull=false, default="CURRENT_TIMESTAMP"
	 */
	public $registrationDate = null;


	/**
	 * Represents hash like this:
	 *
	 * @return  array ('id' => ENTITY_FIELD_INT, 'name'=>ENTITY_FIELD_STRING)
	 */
	public function getFields()
	{
		// TODO: Implement getFields() method.
	}
}
?>