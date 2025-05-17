<?php

require __DIR__ . '/../../dbcon.php';

/* Добавление Склада */
if(isset($_POST['save_info']))
{
    $maslosklad_property_id = mysqli_real_escape_string($con, $_POST['maslosklad_property_id']);
    $purchase_price = mysqli_real_escape_string($con, $_POST['purchase_price']);
    $cash_price = mysqli_real_escape_string($con, $_POST['cash_price']);
    $cashless_price = mysqli_real_escape_string($con, $_POST['cashless_price']);
    
    if($maslosklad_property_id == NULL || $purchase_price == NULL || $cash_price == NULL || $cashless_price == NULL )
    {
        $res = [
            'status' => 422,
            'message' => 'Все поля обязательны для заполнения'
        ];
        echo json_encode($res);
        return;
    }

    $query = "INSERT INTO maslosklad_price (maslosklad_property_id, purchase_price, cash_price, cashless_price ) VALUES ('$maslosklad_property_id','$purchase_price','$cash_price','$cashless_price')";
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

    $maslosklad_property_id = mysqli_real_escape_string($con, $_POST['maslosklad_property_id']);
    $purchase_price = mysqli_real_escape_string($con, $_POST['purchase_price']);
    $cash_price = mysqli_real_escape_string($con, $_POST['cash_price']);
    $cashless_price = mysqli_real_escape_string($con, $_POST['cashless_price']);
   
    if($name == NULL)
    {
        $res = [
            'status' => 422,
            'message' => 'Все поля обязательны для заполнения'
        ];
        echo json_encode($res);
        return;
    }

    $query = "UPDATE maslosklad_price SET 
    maslosklad_property_id='$maslosklad_property_id',
    purchase_price='$purchase_price',
    cash_price='$cash_price',
    cashless_price='$cashless_price'
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


    $query = "SELECT * FROM maslosklad_price WHERE id='$info_id'";

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

?>