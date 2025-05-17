<?php

require __DIR__ . '/../../dbcon.php';

/* Добавление Типа имущества */
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

    $query = "INSERT INTO maslosklad_property_type (name) VALUES ('$name_type')";
    
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


if(isset($_POST['save_info']))
{
    $name = mysqli_real_escape_string($con, $_POST['name']);
    $type = mysqli_real_escape_string($con, $_POST['type_id']);
    $article = mysqli_real_escape_string($con, $_POST['article']);
    
    if($name == NULL || $type == NULL)
    {
        $res = [
            'status' => 422,
            'message' => 'Все поля обязательны для заполнения'
        ];
        echo json_encode($res);
        return;
    }

    $query = "INSERT INTO maslosklad_property (name,type,article) VALUES ('$name','$type','$article')";
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
    $article = mysqli_real_escape_string($con, $_POST['article']);
    $type = mysqli_real_escape_string($con, $_POST['type_id']);
    
    
    if($name == NULL || $type == NULL)
    {
        $res = [
            'status' => 422,
            'message' => 'Все поля обязательны для заполнения'
        ];
        echo json_encode($res);
        return;
    }

    $query = "UPDATE maslosklad_property SET 
    name='$name',
    article='$article',
    type='$type'
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

    $query = "UPDATE maslosklad_property_type SET 
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
        $tbl = 'maslosklad_property';
    }
    if($_GET['for'] == 'type')
    {
        $tbl = 'maslosklad_property_type';
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

/* Удаление Имущества */
if(isset($_POST['delete_info']))
{
    $info_id = mysqli_real_escape_string($con, $_POST['info_id']);

    $query = "UPDATE maslosklad_property SET 
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

/* Удаление Категории имущества */
if(isset($_POST['delete_info_type']))
{
    $info_id = mysqli_real_escape_string($con, $_POST['info_id']);

    $query = "UPDATE maslosklad_property_type SET 
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