<?php

require __DIR__ . '/../../dbcon.php';

/* Признак = Выдача */
$sign_of_calculation = 'vydacha';

if(isset($_POST['save_info']))
{   
    $date_operations = mysqli_real_escape_string($con, $_POST['date_operations']);
    $sign_of_calculation = 'vydacha';
    $buyers_id = mysqli_real_escape_string($con, $_POST['buyers_id']);
    $property_id = mysqli_real_escape_string($con, $_POST['property_id']);
    $count = mysqli_real_escape_string($con, $_POST['count']);
    $comment = mysqli_real_escape_string($con, $_POST['comment']);
    $summa = mysqli_real_escape_string($con, $_POST['summa']);
    $status = mysqli_real_escape_string($con, $_POST['status']);
    $type_pay = mysqli_real_escape_string($con, $_POST['type_pay']);
    $writeoff = isset($_POST['write_off'])? 1 : 0;
    
    if($date_operations == NULL || $property_id == NULL || $count == NULL || $status == NULL || $type_pay == NULL || $summa == NULL || $buyers_id == NULL )
    {
        $res = [
            'status' => 422,
            'message' => 'Все поля обязательны для заполнения'
        ];
        echo json_encode($res);
        return;
    }

    $query = "INSERT INTO maslosklad (date_operations,sign_of_calculation,property_id,count,comment, summa, buyers_id, write_off, status, type_pay) VALUES ('$date_operations','$sign_of_calculation','$property_id','$count','$comment', '$summa', '$buyers_id', '$writeoff', '$status', '$type_pay')";
    
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
    $sign_of_calculation = 'vydacha';
    $buyers_id = mysqli_real_escape_string($con, $_POST['buyers_id']);
    $property_id = mysqli_real_escape_string($con, $_POST['name_property']);
    $count = mysqli_real_escape_string($con, $_POST['count']);
    $comment = mysqli_real_escape_string($con, $_POST['comment']);
    $summa = mysqli_real_escape_string($con, $_POST['summa']);
    $status = mysqli_real_escape_string($con, $_POST['status']);
    $type_pay = mysqli_real_escape_string($con, $_POST['type_pay']);
    $invoice_number = mysqli_real_escape_string($con, $_POST['invoice_number']);
    $writeoff = isset($_POST['write_off'])? 1 : 0;
    
    if($date_operations == NULL || $property_id == NULL || $count == NULL || $status == NULL || $type_pay == NULL || $summa == NULL || $buyers_id == NULL || $writeoff === NULL)
    {
        $res = [
            'status' => 422,
            'message' => 'Все поля обязательны для заполнения'
        ];
        echo json_encode($res);
        return;
    }

    $query = "UPDATE maslosklad SET 
    date_operations='$date_operations',
    buyers_id='$buyers_id',
    property_id='$property_id',
    count='$count',
    status='$status',
    type_pay='$type_pay',
    invoice_number='$invoice_number',
    summa='$summa',
    write_off='$writeoff',
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

if(isset($_POST['relocation_info']))
{   

    $info_relocation_id = mysqli_real_escape_string($con, $_POST['info_relocation_id']);

    $date_operations_relocation = mysqli_real_escape_string($con, $_POST['date_operations_relocation']);
   
    $place_id = mysqli_real_escape_string($con, $_POST['place_id']);
    
    if($info_relocation_id == NULL || $date_operations_relocation == NULL || $place_id == NULL )
    {
        $res = [
            'status' => 422,
            'message' => 'Все поля обязательны для заполнения'
        ];
        echo json_encode($res);
        return;
    }

    $query = "UPDATE maslosklad SET 
    date_operations_relocation='$date_operations_relocation',
    relocation = 1,
    place_id='$place_id'
    WHERE id='$info_relocation_id'";

    
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

    $query = "SELECT * FROM maslosklad WHERE id='$info_id'";

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

if(isset($_GET['info_relocation_id']))
{
    $info_id = mysqli_real_escape_string($con, $_GET['info_relocation_id']);

    $query = "SELECT * FROM maslosklad WHERE id='$info_id'";

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

    $query = "UPDATE maslosklad SET is_deleted = '1' WHERE id='$info_id'";
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

?>