<?php

namespace App\Core;

use App\Helpers\EncodingHelper;

class View {
    private $layout = 'default';
    private $config;
    private $globalData = [];

    public function __construct() {
        $this->config = require __DIR__ . '/../Config/config.php';
    }

    public function setLayout($layout) {
        $this->layout = $layout;
    }

    public function render($view, $data = []) {
        try {
            error_log("DEBUG View::render - Начало рендера вида: {$view}");
            
            // Устанавливаем правильную кодировку UTF-8
            EncodingHelper::setUtf8Headers();
            
            // Объединяем локальные данные с глобальными
            $data = array_merge($this->globalData, $data);
            
            $viewContent = $this->renderView($view, $data);
            
            error_log("DEBUG View::render - Вид отрендерен, длина контента: " . strlen($viewContent));
            
            if ($this->layout) {
                error_log("DEBUG View::render - Начало рендера макета: {$this->layout}");
                $result = $this->renderLayout($viewContent, $data);
                error_log("DEBUG View::render - Макет отрендерен, длина контента: " . strlen($result));
                // Важно: возвращаем результат, а не выводим его напрямую
                return $result;
            }
            
            // Важно: возвращаем результат, а не выводим его напрямую
            return $viewContent;
        } catch (\Exception $e) {
            error_log("ОШИБКА в View::render - " . $e->getMessage());
            error_log("Трассировка: " . $e->getTraceAsString());
            throw $e;
        }
    }

    private function renderView($view, $data) {
        $viewFile = __DIR__ . "/../Views/{$view}.php";
        
        if (!file_exists($viewFile)) {
            error_log("ОШИБКА: Файл вида не найден: {$viewFile}");
            throw new \Exception("View {$view} not found");
        }

        error_log("DEBUG View::renderView - Файл вида существует: {$viewFile}");
        
        extract($data);
        
        ob_start();
        require $viewFile;
        $content = ob_get_clean();
        error_log("DEBUG View::renderView - Вид отрендерен, длина: " . strlen($content));
        return $content;
    }

    private function renderLayout($content, $data) {
        $data['content'] = $content;
        $layoutFile = __DIR__ . "/../Views/layouts/{$this->layout}.php";
        
        if (!file_exists($layoutFile)) {
            error_log("ОШИБКА: Файл макета не найден: {$layoutFile}");
            throw new \Exception("Layout {$this->layout} not found");
        }

        error_log("DEBUG View::renderLayout - Файл макета существует: {$layoutFile}");

        extract($data);
        
        ob_start();
        require $layoutFile;
        $result = ob_get_clean();
        error_log("DEBUG View::renderLayout - Макет отрендерен, длина: " . strlen($result));
        return $result;
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

    /**
     * Установка глобальных данных, доступных во всех шаблонах
     */
    public function setGlobalData($data) {
        $this->globalData = array_merge($this->globalData, $data);
    }
    
    /**
     * Возвращает CSS-класс цвета для типа склада
     * 
     * @param string $warehouseTypeCode Код типа склада
     * @return string CSS-класс цвета
     */
    public function getWarehouseTypeColor($warehouseTypeCode) {
        $colors = [
            'material' => 'primary',    // Материальный склад - синий
            'tool' => 'info',           // Инструментальный склад - голубой
            'oil' => 'warning',         // Склад ГСМ - желтый/оранжевый
            'autoparts' => 'success',   // Склад автозапчастей - зеленый
        ];
        
        return $colors[$warehouseTypeCode] ?? 'secondary'; // По умолчанию серый
    }
} 