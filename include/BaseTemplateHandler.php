<?php
/**
 * TODO: Добавить здесь комментарий
 *
 * PHP version 5
 *
 * @package
 * @author   Eugene Kurbatov <ekur@i-loto.ru>
 */

class BaseTemplateHandler
{
	protected $filename = null;

	public function __construct($filename)
	{
		$this->filename = $filename;
	}

	/**
	 * @return string
	 */
	public function process()
	{
		$content = file_get_contents($this->filename);

		$matches = array();
		preg_match_all("/{[[:upper:]_]*?}/", $content, $matches);

		$values = array();
		foreach ($matches[0] as $p)
		{
			$match = array();
			preg_match("/{([[:upper:]_]*)}/is", $p, $match);

			$methodName = $this->convertVarNameToMethodName($match[1]);
			$values[] = $this->$methodName();
		}

		$patterns = array_map
		(
			function($val)
			{
				return "/$val/";
			},
			$matches[0]
		);
		return preg_replace($patterns, $values, $content);
	}

	private function convertVarNameToMethodName($varName)
	{
		$methodName = "get";
		foreach (explode("_", $varName) as $v)
		{
			$methodName .= ucfirst(strtolower($v));
		}
		return $methodName;
	}
}
