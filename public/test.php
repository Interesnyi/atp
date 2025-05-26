<?php
// Простая тестовая страница для проверки отображения

// Проверка корректной загрузки PHP
echo '<h1>Тестовая страница PHP</h1>';
echo '<p>Текущее время: ' . date('Y-m-d H:i:s') . '</p>';

// Подключаем Bootstrap CSS
echo '<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">';

// Проверка стилей Bootstrap
echo '<div class="container mt-5">';
echo '<div class="row">';
echo '<div class="col-md-6 offset-md-3">';
echo '<div class="card">';
echo '<div class="card-header bg-primary text-white">Это карточка Bootstrap</div>';
echo '<div class="card-body">';
echo '<h5 class="card-title">Если вы видите стили, то Bootstrap работает</h5>';
echo '<p class="card-text">Это тестовая страница для проверки работоспособности базовых функций.</p>';
echo '<a href="/public/maslosklad" class="btn btn-primary">Перейти к складу</a>';
echo '</div>';
echo '</div>';
echo '</div>';
echo '</div>';
echo '</div>';

// Информация о системе
echo '<div class="container mt-3">';
echo '<h4>Информация о PHP:</h4>';
echo '<pre>';
echo 'PHP Version: ' . phpversion() . "\n";
echo 'Document Root: ' . $_SERVER['DOCUMENT_ROOT'] . "\n";
echo 'Server Software: ' . $_SERVER['SERVER_SOFTWARE'] . "\n";
echo 'Server Name: ' . $_SERVER['SERVER_NAME'] . "\n";
echo 'Request URI: ' . $_SERVER['REQUEST_URI'] . "\n";
echo '</pre>';
echo '</div>';
?> 