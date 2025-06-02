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
            // Записываем отладочную информацию в файл
            // file_put_contents(__DIR__ . '/../../logs/debug.log', date('[Y-m-d H:i:s] ') . "View::render - Начало рендера вида: {$view}" . PHP_EOL, FILE_APPEND);
            
            // Устанавливаем правильную кодировку UTF-8
            EncodingHelper::setUtf8Headers();
            
            // Объединяем локальные данные с глобальными
            $data = array_merge($this->globalData, $data);
            
            // Фиксим кодировку данных
            $data = $this->fixArrayEncoding($data);
            
            // Выводим данные для отладки
            // file_put_contents(__DIR__ . '/../../logs/debug.log', date('[Y-m-d H:i:s] ') . "View::render - Данные: " . json_encode(array_keys($data), JSON_UNESCAPED_UNICODE) . PHP_EOL, FILE_APPEND);
            
            // Рендерим представление
            $viewContent = $this->renderView($view, $data);
            
            // file_put_contents(__DIR__ . '/../../logs/debug.log', date('[Y-m-d H:i:s] ') . "View::render - Вид отрендерен, длина контента: " . strlen($viewContent) . PHP_EOL, FILE_APPEND);
            
            if ($this->layout) {
                // file_put_contents(__DIR__ . '/../../logs/debug.log', date('[Y-m-d H:i:s] ') . "View::render - Начало рендера макета: {$this->layout}" . PHP_EOL, FILE_APPEND);
                $result = $this->renderLayout($viewContent, $data);
                // file_put_contents(__DIR__ . '/../../logs/debug.log', date('[Y-m-d H:i:s] ') . "View::render - Макет отрендерен, длина контента: " . strlen($result) . PHP_EOL, FILE_APPEND);
                
                // Выведем содержимое напрямую, чтобы оно отображалось на странице
                echo $result;
                return true;
            }
            
            // Выведем содержимое напрямую, чтобы оно отображалось на странице
            echo $viewContent;
            return true;
        } catch (\Exception $e) {
            // file_put_contents(__DIR__ . '/../../logs/debug.log', date('[Y-m-d H:i:s] ') . "ОШИБКА в View::render - " . $e->getMessage() . PHP_EOL, FILE_APPEND);
            // file_put_contents(__DIR__ . '/../../logs/debug.log', date('[Y-m-d H:i:s] ') . "Трассировка: " . $e->getTraceAsString() . PHP_EOL, FILE_APPEND);
            
            // Выведем ошибку на экран для отладки
            echo '<div style="background-color: #ffeeee; border: 1px solid #ff0000; padding: 10px; margin: 10px;">';
            echo '<h1>Ошибка рендеринга</h1>';
            echo '<p>' . htmlspecialchars($e->getMessage()) . '</p>';
            echo '<pre>' . htmlspecialchars($e->getTraceAsString()) . '</pre>';
            echo '</div>';
            return false;
        }
    }

    private function renderView($view, $data) {
        $viewFile = __DIR__ . "/../Views/{$view}.php";
        
        if (!file_exists($viewFile)) {
            // file_put_contents(__DIR__ . '/../../logs/debug.log', date('[Y-m-d H:i:s] ') . "ОШИБКА: Файл вида не найден: {$viewFile}" . PHP_EOL, FILE_APPEND);
            throw new \Exception("View {$view} not found");
        }

        // file_put_contents(__DIR__ . '/../../logs/debug.log', date('[Y-m-d H:i:s] ') . "View::renderView - Файл вида существует: {$viewFile}" . PHP_EOL, FILE_APPEND);
        
        extract($data);
        
        ob_start();
        require $viewFile;
        $content = ob_get_clean();
        // file_put_contents(__DIR__ . '/../../logs/debug.log', date('[Y-m-d H:i:s] ') . "View::renderView - Вид отрендерен, длина: " . strlen($content) . PHP_EOL, FILE_APPEND);
        
        // Проверка на пустой контент
        if (empty($content)) {
            // file_put_contents(__DIR__ . '/../../logs/debug.log', date('[Y-m-d H:i:s] ') . "ВНИМАНИЕ: Пустой контент при рендеринге {$view}" . PHP_EOL, FILE_APPEND);
        }
        
        return $content;
    }

    private function renderLayout($content, $data) {
        $data['content'] = $content;
        $layoutFile = __DIR__ . "/../Views/layouts/{$this->layout}.php";
        
        if (!file_exists($layoutFile)) {
            // file_put_contents(__DIR__ . '/../../logs/debug.log', date('[Y-m-d H:i:s] ') . "ОШИБКА: Файл макета не найден: {$layoutFile}" . PHP_EOL, FILE_APPEND);
            throw new \Exception("Layout {$this->layout} not found");
        }

        // file_put_contents(__DIR__ . '/../../logs/debug.log', date('[Y-m-d H:i:s] ') . "View::renderLayout - Файл макета существует: {$layoutFile}" . PHP_EOL, FILE_APPEND);

        extract($data);
        
        ob_start();
        require $layoutFile;
        $result = ob_get_clean();
        // file_put_contents(__DIR__ . '/../../logs/debug.log', date('[Y-m-d H:i:s] ') . "View::renderLayout - Макет отрендерен, длина: " . strlen($result) . PHP_EOL, FILE_APPEND);
        
        // Проверка на пустой контент
        if (empty($result)) {
            // file_put_contents(__DIR__ . '/../../logs/debug.log', date('[Y-m-d H:i:s] ') . "ВНИМАНИЕ: Пустой результат после рендеринга макета {$this->layout}" . PHP_EOL, FILE_APPEND);
        }
        
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

    /**
     * Обрабатывает кодировку строки для корректного отображения
     * 
     * @param string $text Текст для обработки
     * @return string Текст в правильной кодировке
     */
    public function fixEncoding($text) {
        if (!mb_check_encoding($text, 'UTF-8') || mb_detect_encoding($text, 'UTF-8', true) === false) {
            $text = mb_convert_encoding($text, 'UTF-8', 'auto');
        }
        return $text;
    }
    
    /**
     * Рекурсивно обрабатывает кодировку в массиве данных
     * 
     * @param array $data Массив данных
     * @return array Массив с исправленной кодировкой
     */
    public function fixArrayEncoding($data) {
        if (!is_array($data)) {
            return $this->fixEncoding($data);
        }
        
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $data[$key] = $this->fixArrayEncoding($value);
            } elseif (is_string($value)) {
                $data[$key] = $this->fixEncoding($value);
            }
        }
        
        return $data;
    }
} 