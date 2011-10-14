<?php
/**
 * Сущность пользователя
 *
 * PHP version 5
 *
 * @package Tools
 * @author  Eugene Kurbatov <ekur@i-loto.ru>
 */

class CopyUser extends Entity
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
	 * @column type="varchar(256)", nullable=false
	 */
	public $password = null;

	/**
	 * Email пользователя
	 *
     * @var string
	 * @column type="varchar(256)", nullable=false
	 */
	public $email = null;

	/**
	 * Номер сотового телефона
	 *
     * @var string
	 * @column type="varchar(256)", nullable=false
	 */
	public $cellPhone = null;

	/**
	 * Статус пользователя
	 *
     * @var string
	 * @column type="varchar(64)", nullable=false
	 */
	public $status = 1;

	/**
	 * Дата регистрации
	 *
     * @var DateTime
	 * @column name="regDate", type="datetime", nullable=false
	 */
	public $registrationDate = null;

	/**
	 * У каждой сущности должен быть идентификатор. Является атрибутом сущности и д.б. объявлен в getFields()
	 *
	 * @column id
	 * @var integer
	 */
	public $id = null;

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