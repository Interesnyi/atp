<?php

require __DIR__ . '/../../dbcon.php';

/* Признак = Доход */
$sign_of_calculation = 'expenses';

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
    $confirm = mysqli_real_escape_string($con, $_POST['confirm']);
    
    if($info_id == NULL || $date_operations == NULL || $sign_of_calculation == NULL || $summa == NULL || $type_payment == NULL || $purpose_payment == NULL || $comment == NULL || $confirm == NULL )
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
    comment='$comment',
    confirm = '$confirm'
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

if(isset($_POST['confirm_claim']))
{
    $info_id = mysqli_real_escape_string($con, $_POST['info_id']);

    $query = "UPDATE balance_income_expenses SET confirm=1 WHERE id='$info_id'";
    $query_run = mysqli_query($con, $query);

    if($query_run)
    {
        $res = [
            'status' => 200,
            'message' => 'Заявка одобрена.'
        ];
        echo json_encode($res);
        return;
    }
    else
    {
        $res = [
            'status' => 500,
            'message' => 'Ошибка!!!'
        ];
        echo json_encode($res);
        return;
    }
}

if(isset($_POST['reject_claim']))
{
    $info_id = mysqli_real_escape_string($con, $_POST['info_id']);

    $query = "UPDATE balance_income_expenses SET confirm=2 WHERE id='$info_id'";
    $query_run = mysqli_query($con, $query);

    if($query_run)
    {
        $res = [
            'status' => 200,
            'message' => 'Заявка отклонена.'
        ];
        echo json_encode($res);
        return;
    }
    else
    {
        $res = [
            'status' => 500,
            'message' => 'Ошибка!!!'
        ];
        echo json_encode($res);
        return;
    }
}


?>