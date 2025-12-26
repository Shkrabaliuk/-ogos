<?php

declare(strict_types=1);

namespace App\Core;

class View
{
    private string $templatesPath;
    private ?string $layout = null;
    private string $content = '';

    public function __construct(?string $templatesPath = null)
    {
        $this->templatesPath = $templatesPath ?? dirname(__DIR__, 2) . '/templates';
    }

    public function render(string $template, array $data = []): string
    {
        $templatePath = $this->getTemplatePath($template);

        if (!file_exists($templatePath)) {
            throw new \RuntimeException("Template [{$template}] not found at [{$templatePath}]");
        }

        // Auto-escape all variables for XSS protection
        $data = $this->escapeData($data);

        // Render the template
        $this->content = $this->renderTemplate($templatePath, $data);

        // If layout is set, render within layout
        if ($this->layout !== null) {
            $layoutPath = $this->getTemplatePath($this->layout);
            
            if (!file_exists($layoutPath)) {
                throw new \RuntimeException("Layout [{$this->layout}] not found at [{$layoutPath}]");
            }

            return $this->renderTemplate($layoutPath, array_merge($data, ['content' => $this->content]));
        }

        return $this->content;
    }

    public function setLayout(string $layout): void
    {
        $this->layout = $layout;
    }

    public function extends(string $layout): void
    {
        $this->setLayout($layout);
    }

    private function renderTemplate(string $path, array $data): string
    {
        extract($data, EXTR_SKIP);

        ob_start();
        include $path;
        return ob_get_clean();
    }

    private function getTemplatePath(string $template): string
    {
        $template = str_replace('.', '/', $template);
        return $this->templatesPath . '/' . $template . '.php';
    }

    private function escapeData(array $data): array
    {
        $escaped = [];

        foreach ($data as $key => $value) {
            if (is_string($value)) {
                $escaped[$key] = $this->escape($value);
            } elseif (is_array($value)) {
                $escaped[$key] = $this->escapeData($value);
            } else {
                $escaped[$key] = $value;
            }
        }

        return $escaped;
    }

    private function escape(string $value): string
    {
        return htmlspecialchars($value, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }

    public function e(string $value): string
    {
        return $this->escape($value);
    }

    public function raw(string $value): string
    {
        return $value;
    }
}
