/**
 * {TABLE_COMMENT}
 *
 * PHP version 5
 *
 * @package Entity
 * @author  {AUTHOR}
 */

class {ENTITY_NAME} extends Entity
{
	/**
	 * Содержит наименование таблицы в БД, где хранятся сущности этого типа. Не является атрибутом сущности
	 *
	 * @var string
	 */
	public $entityTable = "{ENTITY_TABLE}";

	/**
	 * Первичный ключ, обычно соответствует атрибуту "id".  Не является атрибутом сущности.
	 *
	 * @var string
	 */
	public $primaryKey = "{PRIMARY_KEY}";

{FIELDS_META_DATA}

	/**
	 * Возвращает список полей сущности и их типы
	 *
	 * @return array
	 */
	public function getFields()
	{
		return array(
{FIELDS_LIST}
		);
	}
}