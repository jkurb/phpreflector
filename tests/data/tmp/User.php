<?php
/**
 * Таблица содержит информацию о типах пользователей Покупатель
 *
 * PHP version 5
 *
 * @package Entity
 * @author  Eugene Kurbatov
 */

class User extends Entity
{
	/**
	 * Содержит наименование таблицы в БД, где хранятся сущности этого типа. Не является атрибутом сущности
	 *
	 * @var string
	 */
	public $entityTable = "user";

	/**
	 * Первичный ключ, обычно соответствует атрибуту "id".  Не является атрибутом сущности.
	 *
	 * @var string
	 */
	public $primaryKey = "id";


   /**
	* Содержит наименование таблицы в БД, где хранятся сущности этого типа. Не является атрибутом сущности
	*
	* @var string
	* @column type="", allowNull="true", default="user"
	*/
	public $entityTable = "user";

   /**
	* Первичный ключ, обычно соответствует атрибуту "id".  Не является атрибутом сущности.
	*
	* @var string
	* @column type="", allowNull="true", default="id"
	*/
	public $primaryKey = "id";

   /**
	* Email пользователя
	*
	* @var string
	* @column type="varchar(255)", allowNull="false"
	*/
	public $email = null;

   /**
	* Номер телефона
	*
	* @var string
	* @column type="varchar(24)", allowNull="true"
	*/
	public $phone = null;

   /**
	* Пароль для входа
	*
	* @var string
	* @column type="varchar(50)", allowNull="false"
	*/
	public $password = null;

   /**
	* Статус пользователя
	*
	* @var string
	* @column type="varchar(50)", allowNull="false"
	*/
	public $status = null;

   /**
	* Дата регистрации
	*
	* @var DateTime
	* @column type="timestamp", allowNull="false", default="CURRENT_TIMESTAMP"
	*/
	public $creationDate = "CURRENT_TIMESTAMP";

   /**
	* Email для уведомлений
	*
	* @var string
	* @column type="varchar(255)", allowNull="true"
	*/
	public $notificationEmail = null;


	/**
	 * Возвращает список полей сущности и их типы
	 *
	 * @return array
	 */
	public function getFields()
	{
		return array(
            "id" => self::ENTITY_FIELD_INT,
            "email" => self::ENTITY_FIELD_STRING,
            "phone" => self::ENTITY_FIELD_STRING,
            "password" => self::ENTITY_FIELD_STRING,
            "status" => self::ENTITY_FIELD_STRING,
            "creationDate" => self::ENTITY_FIELD_DATETIME,
            "notificationEmail" => self::ENTITY_FIELD_STRING,

		);
	}

/**
	 * Возвращает список полей сущности и их типы
	 *
	 * @return array
	 */
	public function getFields()
	{
		return array(
            "id" => self::ENTITY_FIELD_INT,
            "email" => self::ENTITY_FIELD_STRING,
            "phone" => self::ENTITY_FIELD_STRING,
            "password" => self::ENTITY_FIELD_STRING,
            "status" => self::ENTITY_FIELD_STRING,
            "creationDate" => self::ENTITY_FIELD_DATETIME,
            "notificationEmail" => self::ENTITY_FIELD_STRING,

		);
	}public function test()
	{

	}

}
?>