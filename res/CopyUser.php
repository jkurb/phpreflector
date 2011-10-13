<?php
/**
 * Сущность пользователя
 *
 * PHP version 5
 *
 * @package
 * @author   Eugene Kurbatov <ekur@i-loto.ru>
 */

abstract class CopyUser extends Entity implements Countable, Serializable
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
	 * @datatype varchar
	 * @var string
	 */
    public $password = null;

    /**
	 * Email пользователя
	 * 
	 * @var string
	 */
    public $email = null;

    /**
	 * Номер сотового телефона
	 * 
	 * @var string
	 * @datatype varchar
	 * @len 10
	 * @null false
	 */
    public $cellPhone = null;

    /**
	 * Статус пользователя
	 * 
	 * @var string
	 */
    public $status = "STATUS_UNVERIFIED";

    /**
	 * Дата регистрации
	 * 
	 * @var DateTime
	 */
    public $registrationDate = null;

    /**
	 * Битовая маска проверки логинов пользователя
	 *
	 * @var int
	 */
    public $verifyStatus = 0;

    /**
	 * Имя пользователя
	 *
	 * @var string
	 */
    public $firstName = null;

    /**
	 * Фамилия пользователя
	 *
	 * @var string
	 */
    public static $lastName = 10;

    /**
	 * У каждой сущности должен быть идентификатор. Является атрибутом сущности и д.б. объявлен в getFields()
	 *
	 * @var integer
	 */
    public $id = null;

    const STATUS_UNVERIFIED = "STATUS_UNVERIFIED";

    const STATUS_VERIFIED = "STATUS_VERIFIED";

    const EMAIL_VERIFIED_MASK = 1;

    const CELL_PHONE_VERIFIED_MASK = 2;

    const UNVERIFIED_MASK = 0;

    
    public function __construct($a, $b, $c = 10)
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