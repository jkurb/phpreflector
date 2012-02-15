<?php
/**
 * Обработчик шаблона поля класса
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
		//todo:hardcoded value
		if (is_null($this->field->type))
			return "string";
		else
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
	 * @param $tplFilename Путь к файлу шаблона
	 * @return string
	 */
	public function process($field, $tplFilename)
	{
		$this->field = $field;
		return parent::process($tplFilename);
	}

	private function recognizeDbType($dbType)
	{
		$typeName = preg_replace("/\\W.*/", "", $dbType);

        switch ($typeName)
        {
            case "int": case "tinyint": case "bit":
                return "integer";
            case "float":
                return "float";
            case "double":
                return "double";
            case "decimal":
                return "decimal";
            case "date": case "timestamp": case "datetime":
                return "DateTime";
            case "varchar": case "text": case "tinytext": case "char":
                return "string";
            default:
                throw new Exception("Undefined type '{$typeName}'") ;
        }
	}

	private function getVal($val)
	{
		return is_null($val) ? "null" :
            ((is_numeric($val) || $this->startsWith($val, "self::"))  ? $val :
			    (is_string($val) ? "\"$val\"" : $val)
            );

	}

	private function startsWith($haystack, $needle)
	{
	    $length = strlen($needle);
	    return (substr($haystack, 0, $length) === $needle);
	}
}
