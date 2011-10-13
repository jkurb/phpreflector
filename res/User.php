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
	 * Пароль пользователя
	 *
	 * @column type="varchar(256)", nullable=false
	 * @var string
	 */
	public $password;

	/**
	 * Email пользователя
	 *
	 * @column type="varchar(256)", nullable=false
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
	 * @column type="varchar(64)", nullable=false
	 * @var string
	 */
	public $status = self::STATUS_UNVERIFIED;

	/**
	 * Дата регистрации
	 *
	 * @column type="datetime", nullable=false
	 * @var DateTime
	 */
	public $registrationDate = null;

	/**
	 * Битовая маска проверки логинов пользователя
	 *
	 * @column type="int(11)", nullable=false
	 * @var int
	 */
	public $verifyStatus = self::UNVERIFIED_MASK;

	/**
	 * Имя пользователя
	 *
	 * @column type="varchar(256)", nullable=false
	 * @var string
	 */
	public $firstName = null;

	/**
	 * Фамилия пользователя
	 *
	 * @column type="varchar(256)", nullable=false
	 * @var string
	 */
	public $lastName = null;

	/**
	 * Статус неподтвержденного пользователя
	 */
	const STATUS_UNVERIFIED = "STATUS_UNVERIFIED";

	/**
	 * Статус подтвержденного пользователя
	 */
	const STATUS_VERIFIED = "STATUS_VERIFIED";

	/**
	 * Маска проверки флага верификаци для email
	 * 0x0001
	 */
	const EMAIL_VERIFIED_MASK = 1;

	/**
	 * Маска проверки флага верификаци для номера телефона
	 * 0x0010
	 */
	const CELL_PHONE_VERIFIED_MASK = 2;

	/**
	 * Маска проверки отсутствия верифицированных идентификаторов
	 * 0x0000
	 */
	const UNVERIFIED_MASK = 0;
	

	function __construct($a, $b, $c=10)
	{
		echo "Constr";
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
			"password" => self::ENTITY_FIELD_STRING,
			"email" => self::ENTITY_FIELD_STRING,
			"cellPhone" => self::ENTITY_FIELD_STRING,
			"status" => self::ENTITY_FIELD_STRING,
			"registrationDate" => self::ENTITY_FIELD_TIMESTAMP,
			"verifyStatus" => self::ENTITY_FIELD_INT,
			"firstName" => self::ENTITY_FIELD_STRING,
			"lastName" => self::ENTITY_FIELD_STRING
		);
	}

	public function isVerified()
	{
		return $this->status == self::STATUS_VERIFIED;
	}

	/**
	 * Проверяет установлен ли бит верификации email
	 *
	 * @return int
	 */
	public function canLoginByEmail()
	{
		return $this->verifyStatus & self::EMAIL_VERIFIED_MASK;
	}

	/**
	 * Проверяет установлен ли бит верификации сотового телефона
	 *
	 * @return int
	 */
	public function canLoginByCellPhone()
	{
		return $this->verifyStatus & self::CELL_PHONE_VERIFIED_MASK;
	}

	/**
	 * Устанавливает флаг верификации для email
	 *
	 * @return void
	 */
	public function setEmailVerifiedFlag()
	{
		$this->verifyStatus |= self::EMAIL_VERIFIED_MASK;
	}

	/**
	 * Устанавливает флаг верификации для номера телефона
	 *
	 * @return void
	 */
	public function setCellPhoneVerifiedFlag()
	{
		$this->verifyStatus |= self::CELL_PHONE_VERIFIED_MASK;
	}
}
?>