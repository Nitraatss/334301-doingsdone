<?php
/**
* Функция шаблонизатор
*
* @param string $templateDir путь к файлу шаблона
* @param array $templateData  массив с данными для этого шаблона
*
*/
function include_template($template_dir, $template_data)
{
    if (file_exists($template_dir))
    {
        foreach($template_data as $key => $value)
        {
            ${$key} = $value;
        }
        
        ob_start();
        require_once($template_dir);
        $template = ob_get_contents();
        ob_end_clean();
        
        return $template;
    }

    else
    {
        return("");
    }
}
?>