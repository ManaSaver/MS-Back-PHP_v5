<?php

function renderPage($data = []) 
{
	$rawTemplate = getRawTemplate();
	$TemplateWithData = replaceVarPlaceholders($rawTemplate, $data);
	
	echo $TemplateWithData;
}


/**	
 *	Ця функція об'єднує вміст всіх файлів з шаблонами у єдиний рядок:
 */
function getRawTemplate($filename = 'index.html') 
{
	$string = @file_get_contents('templates/' . $filename);

    if ($string === false) {
        return '[ Помилка! Файл <b>templates/' . $filename . '</b> не існує! ]';
    } else {

		preg_match_all('/<!--(.*?)-->/', $string, $match);

		foreach($match[0] as $key => $value) {
			if (strpos($match[1][$key], 'include') !== false) {
				$string = str_replace($value, getRawTemplate(trim(explode(':', $match[1][$key])[1])), $string);				
			}
		}
		
        return $string;
    }
}

/**	
 *	Ця функція замінює імена змінних у шаблонах на відповідні значення:
 *  Наприклад: {{user_name}} перетвориться у Neo
 */
function replaceVarPlaceholders($string, $data = [])
{
	preg_match_all('/{{(.*?)}}/', $string, $match);

	foreach($match[0] as $key => $value) {
		if(array_key_exists($match[1][$key], $data)) {
			$string = str_replace($match[0][$key], $data[$match[1][$key]], $string);
		} else {
			$string = str_replace($match[0][$key], 'Помилка! Змінної з іменем <b>' . $match[1][$key] . '</b> не існує', $string);
		}
	}
	
	return $string;
}