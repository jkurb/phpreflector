<?php
/**
 * Метаданные сущности
 *
 * PHP version 5
 *
 * @package
 * @author   Eugene Kurbatov <ekur@i-loto.ru>
 */
require_once "Field.php";

class EntityMeta 
{
    /**
     * Название сущности
     *
     * @var null
     */
    public $name = null;

    /**
     * Комментарий
     *
     * @var null
     */
    public $comment = null;

    /**
     * @var Field[]
     */
    public $fields = array();

	/**
     * @var Field[]
     */
    public $constants = array();

	/**
	 * Методы класса
	 *
	 * @var string
	 */
	public $strMethods = null;
}
