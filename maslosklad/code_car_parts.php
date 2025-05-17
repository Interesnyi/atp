<?php

require __DIR__ . '/../dbcon.php';

if(isset($_POST['confirm_claim']))
{
    $info_id = mysqli_real_escape_string($con, $_POST['info_id']);

    $query = "UPDATE maslosklad SET confirm=1 WHERE id='$info_id'";
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

?>