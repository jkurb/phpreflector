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

	public function getFieldComment()
	{
		return $this->field->comment;
	}

	public function getFieldType()
	{
		return $this->recognizeDbType($this->field->type);
	}

	public function getFieldColumnAnnotation()
	{
		return $this->field->getColumnAnnotation();
	}

	public function getFieldColumnName()
	{
		return $this->field->name;
	}

	public function getFieldDefaultValue()
	{
		return $this->getVal($this->field->default);
	}

	/**
	 * @param $field Field
	 *
	 * @return string
	 */
	public function process($field)
	{
		$this->field = $field;
		return parent::process();
	}

	private function recognizeDbType($dbType)
	{
		$typeName = preg_replace("/\\W.*/", "", $dbType);

        switch ($typeName)
        {
            case "int": case "tinyint": case "bit":
                return "integer";
                break;
            case "float":
                return "float";
                break;
            case "double":
                return "double";
                break;
            case "decimal":
                return "decimal";
                break;
            case "date": case "timestamp": case "datetime":
                return "DateTime";
                break;
            case "varchar": case "text": case "tinytext": case "char":
                return "string";
                break;
            default:
                throw new Exception("Undefined type '{$typeName}'") ;
        }
	}

	private function getVal($val)
	{
		return is_null($val) ? "null" :
            (is_numeric($val) ? $val :
			    (is_string($val) ? "\"$val\"" : $val)
            );

	}

}
