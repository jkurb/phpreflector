<?php
/**
 * TODO: Добавить здесь комментарий
 *
 * PHP version 5
 *
 * @package
 * @author   Eugene Kurbatov <ekur@i-loto.ru>
 */

class ClassTemplateHandler extends BaseTemplateHandler
{
	/**
	 * @var EntityMeta
	 */
	protected $entity = null;

	protected $fieldTemplateHandler = null;

	public function __construct($filename, $entity)
	{
		parent::__construct($filename);
		$this->entity = $entity;
	}

	protected function getEntityComment()
	{
		return $this->entity->comment;
	}

	protected function getEntityName()
	{
		return $this->entity->name;
	}

	protected function getEntityMethods()
	{
		return $this->entity->strMethods;
	}



	protected function getEntityConstant()
	{
		$this->entity->constants;
	}

	protected function getEntityFields()
	{
		return $this->entity->fields;
	}



	protected function getCustomFieldList()
	{
		return "";
	}

	protected function getCustomPrimaryKey()
	{
		return "id";
	}

	protected function getCustomEntityTable()
	{
		return strtolower($this->entity->name);
	}

	protected function getCustomAuthor()
	{
		return "Eugene Kurbatov";
	}
}
