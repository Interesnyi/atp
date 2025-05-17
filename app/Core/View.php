<?php

namespace App\Core;

class View {
    private $layout = 'default';
    private $config;

    public function __construct() {
        $this->config = require __DIR__ . '/../Config/config.php';
    }

    public function setLayout($layout) {
        $this->layout = $layout;
    }

    public function render($view, $data = []) {
        $viewContent = $this->renderView($view, $data);
        
        if ($this->layout) {
            return $this->renderLayout($viewContent, $data);
        }
        
        return $viewContent;
    }

    private function renderView($view, $data) {
        $viewFile = __DIR__ . "/../Views/{$view}.php";
        
        if (!file_exists($viewFile)) {
            throw new \Exception("View {$view} not found");
        }

        extract($data);
        
        ob_start();
        require $viewFile;
        return ob_get_clean();
    }

    private function renderLayout($content, $data) {
        $data['content'] = $content;
        $layoutFile = __DIR__ . "/../Views/layouts/{$this->layout}.php";
        
        if (!file_exists($layoutFile)) {
            throw new \Exception("Layout {$this->layout} not found");
        }

        extract($data);
        
        ob_start();
        require $layoutFile;
        return ob_get_clean();
    }

    public function partial($view, $data = []) {
        return $this->renderView($view, $data);
    }

    public function escape($string) {
        return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
    }

    public function assets($path) {
        return $this->config['app']['url'] . '/assets/' . ltrim($path, '/');
    }

    public function url($path = '') {
        return $this->config['app']['url'] . '/' . ltrim($path, '/');
    }
} 