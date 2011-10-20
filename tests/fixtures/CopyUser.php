<?php
/**
 *
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
	 * Пароль пользователя
     *
 	 * @var string
 	 * @column.....
 	 */
	 public $password = null;

    /**
	 * Email пользователя
     *
 	 * @var string
 	 * @column.....
 	 */
	 public $email = null;

    /**
	 * Номер сотового телефона
     *
 	 * @var string
 	 * @column.....
 	 */
	 public $cellPhone = null;

    /**
	 * Статус пользователя
     *
 	 * @var string
 	 * @column.....
 	 */
	 public $status = null;

    /**
	 * Дата регистрации
     *
 	 * @var DateTime
 	 * @column.....
 	 */
	 public $registrationDate = "CURRENT_TIMESTAMP";

    /**
	 * Битовая маска проверки логинов пользователя
     *
 	 * @var integer
 	 * @column.....
 	 */
	 public $verifyStatus = 0;

    /**
	 * Имя пользователя
     *
 	 * @var string
 	 * @column.....
 	 */
	 public $firstName = null;

    /**
	 * Фамилия пользователя
     *
 	 * @var string
 	 * @column.....
 	 */
	 public $lastName = null;



	/**
	 * Возвращает список полей сущности и их типы
	 *
	 * @return array
	 */
	public function getFields()
	{
		return array(
//fields
		);
	}
}

?>