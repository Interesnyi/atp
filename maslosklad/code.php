<?php

require __DIR__ . '/../../dbcon.php';

if(isset($_POST['save_info']))
{
    $number_pass = mysqli_real_escape_string($con, $_POST['number_pass']);
    $tenant_id = mysqli_real_escape_string($con, $_POST['tenant_id']);
    $date_of_issue = mysqli_real_escape_string($con, $_POST['date_of_issue']);
    $type_of_pass = mysqli_real_escape_string($con, $_POST['type_of_pass']);
    $type_of_car = mysqli_real_escape_string($con, $_POST['type_of_car']);
    $car_brand = mysqli_real_escape_string($con, $_POST['car_brand']);
    $state_number = mysqli_real_escape_string($con, $_POST['state_number']);
    $fio_recipient = mysqli_real_escape_string($con, $_POST['fio_recipient']);
    
    if($tenant_id == NULL || $number_pass == NULL || $date_of_issue == NULL || $type_of_pass == NULL || $type_of_car == NULL || $car_brand == NULL || $state_number == NULL || $fio_recipient == NULL)
    {
        $res = [
            'status' => 422,
            'message' => 'Все поля обязательны для заполнения'
        ];
        echo json_encode($res);
        return;
    }

    $query = "INSERT INTO parking_pass (tenant_id,number_pass,date_of_issue,type_of_pass,type_of_car,car_brand,state_number,fio_recipient) VALUES ('$tenant_id','$number_pass','$date_of_issue','$type_of_pass','$type_of_car','$car_brand','$state_number','$fio_recipient')";
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

    $number_pass = mysqli_real_escape_string($con, $_POST['number_pass']);
    $tenant_id = mysqli_real_escape_string($con, $_POST['tenant_id']);
    $date_of_issue = mysqli_real_escape_string($con, $_POST['date_of_issue']);
    $type_of_pass = mysqli_real_escape_string($con, $_POST['type_of_pass']);
    $type_of_car = mysqli_real_escape_string($con, $_POST['type_of_car']);
    $car_brand = mysqli_real_escape_string($con, $_POST['car_brand']);
    $state_number = mysqli_real_escape_string($con, $_POST['state_number']);
    $fio_recipient = mysqli_real_escape_string($con, $_POST['fio_recipient']);

    if($info_id == NULL || $number_pass == NULL)
    {
        $res = [
            'status' => 422,
            'message' => 'Все поля обязательны для заполнения'
        ];
        echo json_encode($res);
        return;
    }

    $query = "UPDATE parking_pass SET 
    number_pass='$number_pass',
    tenant_id='$tenant_id',
    date_of_issue='$date_of_issue',
    type_of_pass='$type_of_pass',
    type_of_car='$type_of_car',
    car_brand='$car_brand',
    state_number='$state_number',
    fio_recipient='$fio_recipient'
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

    $query = "SELECT * FROM parking_pass WHERE id='$info_id'";

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

    $query = "DELETE FROM parking_pass WHERE id='$info_id'";
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