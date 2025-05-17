<?php

require 'dbcon.php';

/* Регистрации пользователя */
if(isset($_POST['sendRegisterForm']))
{
    $surname = mysqli_real_escape_string($con, $_POST['surName']);
    $firstname = mysqli_real_escape_string($con, $_POST['firstName']);
    $secondname = mysqli_real_escape_string($con, $_POST['secondName']);
    $jobtitle = mysqli_real_escape_string($con, $_POST['jobTitle']);
    $loginemail = mysqli_real_escape_string($con, $_POST['loginEmail']);
    $password = mysqli_real_escape_string($con, $_POST['password']);
    $passwordRepeat = mysqli_real_escape_string($con, $_POST['passwordRepeat']); 
    
    if($surname == NULL || $firstname == NULL || $secondname == NULL || $jobtitle == NULL || $loginemail == NULL || $password == NULL || $passwordRepeat == NULL )
    {
        $res = [
            'status' => 422,
            'message' => 'Все поля обязательны для заполнения'
        ];
        echo json_encode($res);
        return;
    }
    
    if($password !== $passwordRepeat )
    {
        $res = [
            'status' => 422,
            'message' => 'Пароли не совпадают'
        ];
        echo json_encode($res);
        return;
    }

    $query = "INSERT INTO users (surname,firstname,secondname,jobtitle,loginemail,password) VALUES ('$surname','$firstname','$secondname','$jobtitle','$loginemail','$password')";
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

/* Авторизация пользователя */
if(isset($_POST['sendLoginForm']))
{
    $loginEmail = mysqli_real_escape_string($con, $_POST['loginEmail']);
    $password = mysqli_real_escape_string($con, $_POST['password']);

    if($loginEmail == NULL || $password == NULL)
    {
        $res = [
            'status' => 422,
            'message' => 'Все поля обязательны для заполнения'
        ];
        echo json_encode($res);
        return;
    }

    $query = "SELECT * FROM users WHERE loginEmail='$loginEmail' AND password='$password'";
    $query_run = mysqli_query($con, $query);

    if(mysqli_num_rows($query_run) == 1)
    {
        $info = mysqli_fetch_array($query_run);
        
        session_start();
        $_SESSION['id'] = $info['id'];
        $_SESSION['loginemail'] = $info['loginEmail'];
        $_SESSION['surname'] = $info['surname'];
        $_SESSION['firstname'] = $info['firstname'];
        $_SESSION['secondname'] = $info['secondname'];
        $_SESSION['jobtitle'] = $info['jobtitle'];
        
        $res = [
            'status' => 200,
            'message' => 'Успешная авторизация',
            'data' => $info
        ];
        echo json_encode($res);
        return;
    }
    else
    {
        $res = [
            'status' => 404,
            'message' => 'Неверный логин или пароль'
        ];
        echo json_encode($res);
        return;
    }
}

?>