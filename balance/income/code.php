<?php

require __DIR__ . '/../../dbcon.php';

/* Признак = Доход */
$sign_of_calculation = 'income';

if(isset($_POST['save_info']))
{
    $date_operations = mysqli_real_escape_string($con, $_POST['date_operations']);
    $summa = mysqli_real_escape_string($con, $_POST['summa']);
    $type_payment = mysqli_real_escape_string($con, $_POST['type_payment']);
    $purpose_payment = mysqli_real_escape_string($con, $_POST['purpose_payment']);
    $comment = mysqli_real_escape_string($con, $_POST['comment']);
    
    if($date_operations == NULL || $summa == NULL || $type_payment == NULL || $purpose_payment == NULL || $comment == NULL )
    {
        $res = [
            'status' => 422,
            'message' => 'Все поля обязательны для заполнения'
        ];
        echo json_encode($res);
        return;
    }

    $query = "INSERT INTO balance_income_expenses (date_operations,sign_of_calculation,summa,type_payment,purpose_payment,comment) VALUES ('$date_operations','$sign_of_calculation','$summa','$type_payment','$purpose_payment','$comment')";
    
    // echo $query;
    
    $query_run = mysqli_query($con, $query);

    if($query_run)
    {
        $res = [
            'status' => 200,
            'message' => 'Данные успешно добавлены'
        ];
        echo json_encode($res);
        return;
    }
    else
    {
        $res = [
            'status' => 500,
            'message' => 'Ошибка! Данные не добавлены!'
        ];
        echo json_encode($res);
        return;
    }
}

if(isset($_POST['update_info']))
{
    $info_id = mysqli_real_escape_string($con, $_POST['info_id']);

    $date_operations = mysqli_real_escape_string($con, $_POST['date_operations']);
   
    $summa = mysqli_real_escape_string($con, $_POST['summa']);
    $type_payment = mysqli_real_escape_string($con, $_POST['type_payment']);
    $purpose_payment = mysqli_real_escape_string($con, $_POST['purpose_payment']);
    $comment = mysqli_real_escape_string($con, $_POST['comment']);

    if($info_id == NULL || $date_operations == NULL || $sign_of_calculation == NULL ||  $summa == NULL || $type_payment == NULL || $purpose_payment == NULL || $comment == NULL )
    {
        $res = [
            'status' => 422,
            'message' => 'Все поля обязательны для заполнения'
        ];
        echo json_encode($res);
        return;
    }

    $query = "UPDATE balance_income_expenses SET 
    date_operations='$date_operations',
    sign_of_calculation='$sign_of_calculation',
    summa='$summa',
    type_payment='$type_payment',
    purpose_payment='$purpose_payment',
    comment='$comment'
    WHERE id='$info_id'";
    
    $query_run = mysqli_query($con, $query);

    if($query_run)
    {
        $res = [
            'status' => 200,
            'message' => 'Данные успешно обновлены'
        ];
        echo json_encode($res);
        return;
    }
    else
    {
        $res = [
            'status' => 500,
            'message' => 'Ошибка! Данные не обновлены!'
        ];
        echo json_encode($res);
        return;
    }
}



if(isset($_GET['info_id']))
{
    $info_id = mysqli_real_escape_string($con, $_GET['info_id']);

    $query = "SELECT * FROM balance_income_expenses WHERE id='$info_id'";

    $query_run = mysqli_query($con, $query);

    if(mysqli_num_rows($query_run) == 1)
    {
        $info = mysqli_fetch_array($query_run);

        $res = [
            'status' => 200,
            'message' => 'Данные успешно найдены',
            'data' => $info
        ];
        echo json_encode($res);
        return;
    }
    else
    {
        $res = [
            'status' => 404,
            'message' => 'Ошибка! Данные не найдены!'
        ];
        echo json_encode($res);
        return;
    }
}

if(isset($_POST['delete_info']))
{
    $info_id = mysqli_real_escape_string($con, $_POST['info_id']);

    $query = "UPDATE balance_income_expenses SET is_deleted = 1 WHERE id='$info_id'";
    $query_run = mysqli_query($con, $query);

    if($query_run)
    {
        $res = [
            'status' => 200,
            'message' => 'Данные успешно удалены.'
        ];
        echo json_encode($res);
        return;
    }
    else
    {
        $res = [
            'status' => 500,
            'message' => 'Ошибка! Данные не удалены!'
        ];
        echo json_encode($res);
        return;
    }
}

if(isset($_POST['get_vydacha']))
{
	$query = "ALTER TABLE balance_income_expenses 
	ADD UNIQUE INDEX unique_transaction (summa, date_operations, comment);
	
	INSERT IGNORE INTO balance_income_expenses 
	(sign_of_calculation, payment_method, summa, type_payment, purpose_payment, comment, date_operations, confirm, history)
	SELECT 
		'income' as sign_of_calculation,
		'cash' as payment_method,
		s.summa,
		'2' as type_payment,
		spt.name as purpose_payment,
		CONCAT(s.count, ' ', sp.name) as comment,
		s.date_operations,
		1 as confirm,
		1 as history
	FROM `maslosklad` s 
	LEFT JOIN `maslosklad_property` sp ON s.property_id = sp.id 
	LEFT JOIN `maslosklad_property_type` spt ON sp.type = spt.id
	LEFT JOIN `maslosklad_property_place` spp ON s.place_id = spp.id
	LEFT JOIN `maslosklad_buyers` mb ON s.buyers_id = mb.id
	WHERE s.is_deleted = 0 
	AND s.sign_of_calculation = 'vydacha'  
	AND s.type_pay = 0
	AND s.write_off = 0 
	AND s.relocation = 0 
	AND s.status = 1";
	
    $query_run = mysqli_query($con, $query);

    if($query_run)
    {
        $res = [
            'status' => 200,
            'message' => 'Данные успешно подтянуты со Склада.'
        ];
        echo json_encode($res);
        return;
    }
    else
    {
        $res = [
            'status' => 500,
            'message' => 'Ошибка! Данные не подтянуты из Склада!'
        ];
        echo json_encode($res);
        return;
    }
	
}

?>