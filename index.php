<?php
// start the session
session_start();

// Вывод ошибок

//ini_set('error_reporting', E_ALL);
//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);

// Если пользователь авторизован, перенаправляем на /maslosklad/
if (isset($_SESSION["id"]) && $_SESSION["id"] > 0) {
    header("location: /maslosklad/");
    exit;
}

?>
<!doctype html>
<html lang="en">
    <head>
        <!-- Required meta tags -->
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        
        <!-- Bootstrap CSS -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
        <link rel="stylesheet" href="//cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/css/alertify.min.css"/>
        <title>ELDIR | Главная страница </title>
        
    </head>
<body>
<? require_once('nav.php'); ?>
    
    <!-- Форма авторизации -->
    <div class="modal fade" id="loginModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Форма авторизации</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="loginForm">
                <div class="modal-body">

                    <div id="errorMessageLogin" class="alert alert-warning d-none"></div>

                    <div class="mb-3">
                        <label for="">Логин (e-mail)</label>
                        <input type="text" name="loginEmail" class="form-control" />
                    </div>
                    
                    <div class="mb-3">
                        <label for="">Пароль</label>
                        <input type="password" name="password" class="form-control" />
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Закрыть</button>
                    <button type="submit" class="btn btn-primary">Войти</button>
                </div>
            </form>
            </div>
        </div>
    </div> 
    
    <!-- Форма регистрации -->
    <div class="modal fade" id="registerModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true" style="display:none;">
        <div class="modal-dialog">
            <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Форма регистрации</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="registerForm">
                <div class="modal-body">

                    <div id="errorMessageRegister" class="alert alert-warning d-none"></div>

                    <div class="mb-3">
                        <label for="">Фамилия</label>
                        <input type="text" name="surName" class="form-control" />
                    </div>
                    
                    <div class="mb-3">
                        <label for="">Имя</label>
                        <input type="text" name="firstName" class="form-control" />
                    </div>
                    
                    <div class="mb-3">
                        <label for="">Отчество</label>
                        <input type="text" name="secondName" class="form-control" />
                    </div>
                    
                    <div class="mb-3">
                        <label for="">Должность</label>
                        <input type="text" name="jobTitle" class="form-control" />
                    </div>
                    
                    <div class="mb-3">
                        <label for="">Логин (e-mail)</label>
                        <input type="text" name="loginEmail" class="form-control" />
                    </div>
                    
                    <div class="mb-3">
                        <label for="">Пароль</label>
                        <input type="password" name="password" class="form-control" />
                    </div>
                    
                    <div class="mb-3">
                        <label for="">Повторите пароль</label>
                        <input type="password" name="passwordRepeat" class="form-control" />
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Закрыть</button>
                    <button type="submit" class="btn btn-primary">Войти</button>
                </div>
            </form>
            </div>
        </div>
    </div>
    
    
<div class="container mt-4">
    <div class="row">
        <div class="col-md-12">
            <h1>ELDIR | Система управления организацией</h1>
        </div>
    </div>
    
    <?php
        if(isset($_SESSION['id']))
        {
    ?>
            <div class="row">
                <div class="col-md-8">
                    <h2>Добро пожаловать в ELDIR!</h2>
                </div>
            </div>
    <?php
        }
        else
        {
    ?>
            <div class="row">
                <div class="col-md-12">
                    <div class="btn-group mt-3">
                        <a href="" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#loginModal">Войти</a>
                        <a href="" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#registerModal" style="display:none;">Зарегистрироваться</a>
                    </div>
                </div>
            </div>
    <?php
        }
    ?>
</div>
    <!--
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="//cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/alertify.min.js"></script>
    -->
    <? require_once('js.php'); ?>
    
    <script>
        /* Авторизация */
        $(document).on('submit', '#loginForm', function (e) {
            e.preventDefault();

            var formData = new FormData(this);
            formData.append("sendLoginForm", true);

            $.ajax({
                type: "POST",
                url: getCodePath(),
                data: formData,
                processData: false,
                contentType: false,
                success: function (response) {
                    
                    var res = jQuery.parseJSON(response);
                    if(res.status == 422) {
                        $('#errorMessageLogin').removeClass('d-none');
                        $('#errorMessageLogin').text(res.message);

                    }else if(res.status == 200){

                        $('#errorMessageLogin').addClass('d-none');
                        $('#loginModal').modal('hide');
                        $('#loginForm')[0].reset();

                        alertify.set('notifier','position', 'top-right');
                        alertify.success(res.message);
                        
                        setTimeout(function(){
                          location.reload();
                        }, 2000);
                        
                    }else if(res.status == 500) {
                        alert(res.message);
                    }
                }
            });

        });
        
        /* Регистрация */
        $(document).on('submit', '#registerForm', function (e) {
            e.preventDefault();

            var formData = new FormData(this);
            formData.append("sendRegisterForm", true);

            $.ajax({
                type: "POST",
                url: getCodePath(),
                data: formData,
                processData: false,
                contentType: false,
                success: function (response) {
                    
                    var res = jQuery.parseJSON(response);
                    if(res.status == 422) {
                        $('#errorMessageRegister').removeClass('d-none');
                        $('#errorMessageRegister').text(res.message);

                    }else if(res.status == 200){

                        $('#errorMessageRegister').addClass('d-none');
                        $('#registerModal').modal('hide');
                        $('#registerModal')[0].reset();

                        alertify.set('notifier','position', 'top-right');
                        alertify.success(res.message);

                        $('#myTable').load(location.href + " #myTable");

                    }else if(res.status == 500) {
                        alert(res.message);
                    }
                }
            });

        });
        
    </script>
</body>
</html>