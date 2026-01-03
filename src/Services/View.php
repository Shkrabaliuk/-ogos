<?php
namespace App\Services;

class View
{
    public static function icon($name)
    {
        $path = __DIR__ . '/../../assets/icons/' . $name . '.svg';
        if (file_exists($path)) {
            return file_get_contents($path);
        }
        return '';
    }

    public static function render($template, $data = [])
    {
        extract($data);
        ob_start();
        require __DIR__ . '/../../templates/' . $template . '.php';
        return ob_get_clean();
    }
}
