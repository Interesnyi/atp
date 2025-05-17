<?php
// Предотвращаем доступ без авторизации
session_start();
if (!isset($_SESSION["id"])) {
    header("location: /");
    exit;
}

// Подключаем базу данных
require __DIR__ . '/../../dbcon.php';

// Проверяем наличие параметра номера счета
if(isset($_GET['invoice_number']) && !empty($_GET['invoice_number'])) {
    $invoice_number = mysqli_real_escape_string($con, $_GET['invoice_number']);
    
    // Запрос на получение данных счета
    $query = "SELECT s.date_operations, mb.name as buyers_name, sp.name as property_name, 
                     spt.name as type_name, s.count, s.summa,
                     IF(s.status > 0, 'Оплачен', 'Не оплачен') AS status, 
                     IF(s.type_pay > 0, 'безнал', 'нал') AS type_pay,
                     s.comment, s.invoice_number
              FROM `maslosklad` s 
              LEFT JOIN `maslosklad_property` sp ON s.property_id = sp.id 
              LEFT JOIN `maslosklad_property_type` spt ON sp.type = spt.id
              LEFT JOIN `maslosklad_buyers` mb ON s.buyers_id = mb.id
              WHERE s.is_deleted = 0 
              AND s.sign_of_calculation = 'vydacha' 
              AND s.invoice_number = '$invoice_number'
              ORDER BY s.date_operations";
    
    $result = mysqli_query($con, $query);
    
    if($result && mysqli_num_rows($result) > 0) {
        // Получаем сумму по счету
        $total_query = "SELECT SUM(s.summa) as total_sum 
                      FROM `maslosklad` s 
                      WHERE s.is_deleted = 0 
                      AND s.sign_of_calculation = 'vydacha' 
                      AND s.invoice_number = '$invoice_number'";
        $total_result = mysqli_query($con, $total_query);
        $total_sum = 0;
        
        if($total_result && $total_row = mysqli_fetch_assoc($total_result)) {
            $total_sum = $total_row['total_sum'];
        }
        
        // Устанавливаем заголовки для скачивания файла
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=invoice_' . $invoice_number . '_' . date('Y-m-d') . '.csv');
        
        // Создаем PHP выходной поток
        $output = fopen('php://output', 'w');
        
        // Устанавливаем UTF-8 BOM для корректного отображения кириллицы в Excel
        fputs($output, "\xEF\xBB\xBF");
        
        // Заголовки столбцов
        fputcsv($output, [
            'Дата', 
            'Номер счета', 
            'Покупатель', 
            'Наименование', 
            'Тип', 
            'Количество', 
            'Сумма (руб.)', 
            'Тип оплаты', 
            'Статус', 
            'Комментарий'
        ], ';');
        
        // Данные
        while($row = mysqli_fetch_assoc($result)) {
            // Формируем дату в формате ДД.ММ.ГГГГ
            $date_formatted = date('d.m.Y', strtotime($row['date_operations']));
            
            fputcsv($output, [
                $date_formatted,
                $row['invoice_number'],
                $row['buyers_name'],
                $row['property_name'],
                $row['type_name'],
                $row['count'],
                $row['summa'],
                $row['type_pay'],
                $row['status'],
                $row['comment']
            ], ';');
        }
        
        // Добавляем строку с итогом
        fputcsv($output, [], ';');
        fputcsv($output, ['', '', '', '', '', 'ИТОГО:', number_format($total_sum, 2, '.', ' '), '', '', ''], ';');
        
        // Закрываем выходной поток
        fclose($output);
        exit;
    } else {
        // Если нет данных, перенаправляем обратно
        header("location: index.php?error=no_data_found");
        exit;
    }
} else {
    // Если не указан номер счета, перенаправляем обратно
    header("location: index.php?error=no_invoice_number");
    exit;
}
?> 