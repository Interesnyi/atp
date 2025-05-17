<?php
// Предотвращаем доступ без авторизации
session_start();
if (!isset($_SESSION["id"])) {
    $response = [
        'status' => 403,
        'message' => 'Доступ запрещен'
    ];
    echo json_encode($response);
    exit;
}

// Подключаем базу данных
require __DIR__ . '/../../dbcon.php';

// Проверяем наличие параметра номера счета
if(isset($_GET['invoice_number']) && !empty($_GET['invoice_number'])) {
    $invoice_number = mysqli_real_escape_string($con, $_GET['invoice_number']);
    
    // Запрос на получение суммы по счету
    $query = "SELECT SUM(s.summa) as total_sum 
              FROM `maslosklad` s 
              WHERE s.is_deleted = 0 
              AND s.sign_of_calculation = 'vydacha' 
              AND s.invoice_number LIKE '%$invoice_number%'";
    
    $result = mysqli_query($con, $query);
    
    if($result && $row = mysqli_fetch_assoc($result)) {
        $total_sum = $row['total_sum'];
        
        // Проверяем наличие результатов
        if($total_sum) {
            // Форматируем сумму для читаемости
            $formatted_sum = number_format($total_sum, 2, '.', ' ');
            
            $response = [
                'status' => 200,
                'message' => 'Данные успешно получены',
                'total_sum' => $formatted_sum
            ];
        } else {
            $response = [
                'status' => 404,
                'message' => 'По данному номеру счета не найдено записей',
                'total_sum' => '0.00'
            ];
        }
    } else {
        $response = [
            'status' => 500,
            'message' => 'Ошибка при выполнении запроса',
            'error' => mysqli_error($con)
        ];
    }
} else {
    $response = [
        'status' => 400,
        'message' => 'Не указан номер счета'
    ];
}

// Возвращаем результат в JSON формате
header('Content-Type: application/json');
echo json_encode($response);
?> 