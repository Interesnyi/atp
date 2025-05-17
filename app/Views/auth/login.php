<div class="row justify-content-center mt-5">
    <div class="col-md-6 col-lg-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Форма авторизации</h5>
            </div>
            <div class="card-body">
                <form id="loginForm" method="post" action="/login">
                    <div id="errorMessageLogin" class="alert alert-warning d-none"></div>

                    <div class="mb-3">
                        <label for="loginEmail">Логин (e-mail)</label>
                        <input type="text" name="loginEmail" id="loginEmail" class="form-control" />
                    </div>
                    
                    <div class="mb-3">
                        <label for="password">Пароль</label>
                        <input type="password" name="password" id="password" class="form-control" />
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">Войти</button>
                    </div>
                </form>
            </div>
            <div class="card-footer text-center">
                <a href="/" class="btn btn-link">Вернуться на главную</a>
                <a href="/register" class="btn btn-link">Регистрация</a>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    $('#loginForm').submit(function(e) {
        e.preventDefault();
        
        var formData = {
            'loginEmail': $('#loginEmail').val(),
            'password': $('#password').val()
        };
        
        $.ajax({
            type: 'POST',
            url: '/login',
            data: formData,
            dataType: 'json',
            encode: true,
            success: function(data) {
                if (data.status === 200) {
                    $('#errorMessageLogin').removeClass('d-none alert-warning').addClass('alert-success').text(data.message);
                    setTimeout(function() {
                        window.location.href = data.redirect || '/dashboard';
                    }, 1000);
                } else {
                    $('#errorMessageLogin').removeClass('d-none').text(data.message);
                }
            },
            error: function(xhr, status, error) {
                console.error('Ошибка:', error);
                console.log('Статус:', status);
                console.log('Ответ сервера:', xhr.responseText);
                $('#errorMessageLogin').removeClass('d-none').text('Произошла ошибка при отправке запроса');
            }
        });
    });
});
</script> 