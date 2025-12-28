<?php

declare(strict_types=1);

namespace App\Core;

class View
{
    private string $templatesPath;
    private ?string $layout = null;
    private array $sections = [];
    private ?string $currentSection = null;

    public function __construct(?string $templatesPath = null)
    {
        $this->templatesPath = $templatesPath ?? dirname(__DIR__, 2) . '/templates';
    }

    public function render(string $template, array $data = []): string
    {
        $templateFile = $this->templatesPath . '/' . $template . '.php';

        if (!file_exists($templateFile)) {
            throw new \RuntimeException("Template not found: {$template}");
        }

        $data = $this->escapeData($data);

        $content = $this->renderTemplate($templateFile, $data);

        if ($this->layout !== null) {
            $layoutFile = $this->templatesPath . '/layouts/' . $this->layout . '.php';
            if (!file_exists($layoutFile)) {
                throw new \RuntimeException("Layout not found: {$this->layout}");
            }
            $data['content'] = $content;
            $content = $this->renderTemplate($layoutFile, $data);
            $this->layout = null;
        }

        return $content;
    }

    private function renderTemplate(string $file, array $data): string
    {
        extract($data, EXTR_SKIP);

        ob_start();
        include $file;
        return ob_get_clean();
    }

    private function escapeData(array $data): array
    {
        $escaped = [];

        foreach ($data as $key => $value) {
            if (is_string($value)) {
                $escaped[$key] = htmlspecialchars($value, ENT_QUOTES | ENT_HTML5, 'UTF-8');
            } elseif (is_array($value)) {
                $escaped[$key] = $this->escapeData($value);
            } else {
                $escaped[$key] = $value;
            }
        }

        return $escaped;
    }

    public function setLayout(string $layout): void
    {
        $this->layout = $layout;
    }

    public function raw(string $value): string
    {
        return $value;
    }

    public function startSection(string $name): void
    {
        $this->currentSection = $name;
        ob_start();
    }

    public function endSection(): void
    {
        if ($this->currentSection === null) {
            throw new \RuntimeException('No section started');
        }

        $this->sections[$this->currentSection] = ob_get_clean();
        $this->currentSection = null;
    }

    public function section(string $name): string
    {
        return $this->sections[$name] ?? '';
    }

    public function setTemplatesPath(string $path): void
    {
        $this->templatesPath = $path;
    }
}
