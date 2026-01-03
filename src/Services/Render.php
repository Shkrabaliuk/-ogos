<?php
namespace App\Services;

use League\CommonMark\CommonMarkConverter;
use League\CommonMark\Exception\CommonMarkException;

class Render
{
    /**
     * Convert Markdown to HTML
     * @param string|null $markdown
     * @return string
     */
    public static function html($markdown)
    {
        if (empty($markdown)) {
            return '';
        }

        try {
            // Використовуємо бібліотеку League/CommonMark
            $converter = new CommonMarkConverter([
                'html_input' => 'escape', // Захист від XSS
                'allow_unsafe_links' => false,
            ]);

            return $converter->convert($markdown);
        } catch (\Throwable $e) {
            // Якщо бібліотека не спрацювала, повертаємо текст як є (або логуємо помилку)
            return nl2br(htmlspecialchars($markdown));
        }
    }
}
