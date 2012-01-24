<?php
/**
 * TODO: Добавить здесь комментарий
 *
 * PHP version 5
 *
 * @package
 * @author   Eugene Kurbatov <ekur@i-loto.ru>
 */

class FieldTemplateHandler extends BaseTemplateHandler
{
	/**
	 * @var Field
	 */
	protected $field = null;

	protected function getFieldComment()
	{
		return $this->field->comment;
	}

	protected function getFieldType()
	{
		return "Type";
	}

	protected function getFieldColomnAnnotation()
	{
		return "ColomnAnnotation";
	}

	protected function getFieldColomnName()
	{
		return $this->field->name;
	}

	protected function getFieldDefaultValue()
	{
		return $this->field->default;
	}
}
