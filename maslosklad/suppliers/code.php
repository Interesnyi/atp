<?php

require __DIR__ . '/../../dbcon.php';

/* Добавление Покупателя */
if(isset($_POST['save_info_type']))
{
    $name_type = mysqli_real_escape_string($con, $_POST['name_type']);
    
    if($name_type == NULL)
    {
        $res = [
            'status' => 422,
            'message' => 'Все поля обязательны для заполнения'
        ];
        echo json_encode($res);
        return;
    }

    $query = "INSERT INTO maslosklad_buyers (name) VALUES ('$name_type')";
    
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

/* Добавление Поставщика */
if(isset($_POST['save_info']))
{
    $name = mysqli_real_escape_string($con, $_POST['name']);
    
    if($name == NULL)
    {
        $res = [
            'status' => 422,
            'message' => 'Все поля обязательны для заполнения'
        ];
        echo json_encode($res);
        return;
    }

    $query = "INSERT INTO maslosklad_suppliers (name) VALUES ('$name')";
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

    $name = mysqli_real_escape_string($con, $_POST['name']);
   
    if($name == NULL)
    {
        $res = [
            'status' => 422,
            'message' => 'Все поля обязательны для заполнения'
        ];
        echo json_encode($res);
        return;
    }

    $query = "UPDATE maslosklad_suppliers SET 
    name='$name'
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

if(isset($_POST['update_info_type']))
{
    $info_id = mysqli_real_escape_string($con, $_POST['info_id']);

    $name_type = mysqli_real_escape_string($con, $_POST['name_type']);
    
    if($name_type == NULL)
    {
        $res = [
            'status' => 422,
            'message' => 'Все поля обязательны для заполнения'
        ];
        echo json_encode($res);
        return;
    }

    $query = "UPDATE maslosklad_buyers SET 
    name='$name_type'
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
    
    if($_GET['for'] == 'property')
    {
        $tbl = 'maslosklad_suppliers';
    }
    if($_GET['for'] == 'type')
    {
        $tbl = 'maslosklad_suppliers';
    }

    $query = "SELECT * FROM ".$tbl." WHERE id='$info_id'";

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

/* Удаление Поставщика */
if(isset($_POST['delete_info']))
{
    $info_id = mysqli_real_escape_string($con, $_POST['info_id']);

    $query = "UPDATE maslosklad_suppliers SET 
    is_deleted=1
    WHERE id='$info_id'";
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

/* Удаление Покупателя */
if(isset($_POST['delete_info_type']))
{
    $info_id = mysqli_real_escape_string($con, $_POST['info_id']);

    $query = "UPDATE maslosklad_buyers SET 
    is_deleted=1
    WHERE id='$info_id'";
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