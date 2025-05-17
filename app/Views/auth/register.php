<div class="row justify-content-center mt-5">
    <div class="col-md-8 col-lg-6">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Регистрация пользователя</h5>
            </div>
            <div class="card-body">
                <form id="registerForm" method="post" action="/register">
                    <div id="errorMessageRegister" class="alert alert-warning d-none"></div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="surName">Фамилия</label>
                            <input type="text" name="surName" id="surName" class="form-control" />
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="firstName">Имя</label>
                            <input type="text" name="firstName" id="firstName" class="form-control" />
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="secondName">Отчество</label>
                            <input type="text" name="secondName" id="secondName" class="form-control" />
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="jobTitle">Должность</label>
                        <input type="text" name="jobTitle" id="jobTitle" class="form-control" />
                    </div>

                    <div class="mb-3">
                        <label for="loginEmail">Email</label>
                        <input type="email" name="loginEmail" id="loginEmail" class="form-control" />
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="password">Пароль</label>
                            <input type="password" name="password" id="password" class="form-control" />
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="passwordRepeat">Повторите пароль</label>
                            <input type="password" name="passwordRepeat" id="passwordRepeat" class="form-control" />
                        </div>
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">Зарегистрироваться</button>
                    </div>
                </form>
            </div>
            <div class="card-footer text-center">
                <a href="/login" class="btn btn-link">Уже есть аккаунт? Войти</a>
                <a href="/" class="btn btn-link">Вернуться на главную</a>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    $('#registerForm').submit(function(e) {
        e.preventDefault();
        
        var formData = {
            'surName': $('#surName').val(),
            'firstName': $('#firstName').val(),
            'secondName': $('#secondName').val(),
            'jobTitle': $('#jobTitle').val(),
            'loginEmail': $('#loginEmail').val(),
            'password': $('#password').val(),
            'passwordRepeat': $('#passwordRepeat').val()
        };
        
        $.ajax({
            type: 'POST',
            url: '/register',
            data: formData,
            dataType: 'json',
            encode: true,
            success: function(data) {
                if (data.status === 200) {
                    $('#errorMessageRegister').removeClass('alert-warning').addClass('alert-success').removeClass('d-none').text(data.message);
                    setTimeout(function() {
                        window.location.href = '/login';
                    }, 2000);
                } else {
                    $('#errorMessageRegister').removeClass('d-none').text(data.message);
                }
            },
            error: function(xhr, status, error) {
                console.error('Ошибка:', error);
                console.log('Статус:', status);
                console.log('Ответ сервера:', xhr.responseText);
                $('#errorMessageRegister').removeClass('d-none').text('Произошла ошибка при отправке запроса');
            }
        });
    });
});
</script> 