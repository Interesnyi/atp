<?php

require __DIR__ . '/../../dbcon.php';

/* Признак = Доход */
$sign_of_calculation = 'income';

if(isset($_POST['save_info']))
{   
    $date_operations = mysqli_real_escape_string($con, $_POST['date_operations']);
    $sign_of_calculation = 'priemka';
    $suppliers_id = mysqli_real_escape_string($con,$_POST['suppliers_id']);
    $property_id = mysqli_real_escape_string($con, $_POST['property_id']);
    $place_id = mysqli_real_escape_string($con, $_POST['place_id']);
    $count = mysqli_real_escape_string($con, $_POST['count']);
    $comment = mysqli_real_escape_string($con, $_POST['comment']);
	$summa = mysqli_real_escape_string($con, $_POST['summa']);
    
    if($date_operations == NULL || $property_id == NULL || $place_id == NULL || $count == NULL  || $suppliers_id == NULL )
    {
        $res = [
            'status' => 422,
            'message' => 'Все поля обязательны для заполнения'
        ];
        echo json_encode($res);
        return;
    }

    $query = "INSERT INTO maslosklad (date_operations,sign_of_calculation,property_id,place_id,count,comment,suppliers_id,summa) VALUES ('$date_operations','$sign_of_calculation','$property_id','$place_id','$count','$comment','$suppliers_id','$summa')";
    
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
    
    $suppliers_id = mysqli_real_escape_string($con,$_POST['suppliers_id']);
    $property_id = mysqli_real_escape_string($con, $_POST['name_property']);
    $place_id = mysqli_real_escape_string($con, $_POST['place_id']);
    $count = mysqli_real_escape_string($con, $_POST['count']);
    $comment = mysqli_real_escape_string($con, $_POST['comment']);
	$summa = mysqli_real_escape_string($con, $_POST['summa']);

    if($info_id == NULL || $date_operations == NULL || $property_id == NULL || $place_id == NULL || $count == NULL || $suppliers_id == NULL )
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
    suppliers_id='$suppliers_id',
    property_id='$property_id',
    place_id='$place_id',
    count='$count',
	summa='$summa',
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

    $query = "UPDATE maslosklad SET is_deleted=1 WHERE id='$info_id'";
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