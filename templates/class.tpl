/**
 * {ENTITY_COMMENT}
 *
 * PHP version 5
 *
 * @package Entity
 * @author  {CUSTOM_AUTHOR}
 */

class {ENTITY_NAME} extends Entity
{
	/**
	 * Содержит наименование таблицы в БД, где хранятся сущности этого типа. Не является атрибутом сущности
	 *
	 * @var string
	 */
	public $entityTable = "{CUSTOM_ENTITY_TABLE}";

	/**
	 * Первичный ключ, обычно соответствует атрибуту "id".  Не является атрибутом сущности.
	 *
	 * @var string
	 */
	public $primaryKey = "{CUSTOM_PRIMARY_KEY}";

{ENTITY_CONSTANTS}
{ENTITY_FIELDS}
	/**
	 * Возвращает список полей сущности и их типы
	 *
	 * @return array
	 */
	public function getFields()
	{
		return array(
{CUSTOM_FIELDS_LIST}
		);
	}

{ENTITY_METHODS}

}