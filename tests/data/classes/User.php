<?php
/**
 * Таблица содержит информацию о типах пользователей Покупатель
 *
 * PHP version 5
 *
 * @category BL
 * @package  Entity
 * @author   Andrey Filippov <afi@i-loto.ru>
 * @license  %license% name
 * @version  SVN: $Id: User.php 9 2007-12-25 11:26:03Z afi $
 * @link     nolink
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
	 * @column type="timestamp", allowNull="false"
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
	 * Статус неподтвержденного пользователя
	 */
	const STATUS_UNVERIFIED = "STATUS_UNVERIFIED";

	/**
	 * Статус подтвержденного пользователя
	 */
	const STATUS_VERIFIED = "STATUS_VERIFIED";


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
			"creationDate" => self::ENTITY_FIELD_TIMESTAMP,
			"notificationEmail" => self::ENTITY_FIELD_STRING,

		);
	}
}

?>