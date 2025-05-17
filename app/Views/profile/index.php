<div class="container mt-4">
    <div class="row mb-4">
        <div class="col-md-12">
            <h1>Профиль пользователя</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/dashboard">Панель управления</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Профиль</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4 mb-4">
            <div class="card">
                <div class="card-body text-center">
                    <i class="bi bi-person-circle fs-1 mb-3 text-primary"></i>
                    <h5 class="card-title">Личные данные</h5>
                    <div class="list-group list-group-flush">
                        <div class="list-group-item">
                            <small class="text-muted d-block">ФИО</small>
                            <span class="fw-bold">
                                <?= htmlspecialchars($user['surName'] ?? '') ?> 
                                <?= htmlspecialchars($user['firstName'] ?? '') ?> 
                                <?= htmlspecialchars($user['secondName'] ?? '') ?>
                            </span>
                        </div>
                        <div class="list-group-item">
                            <small class="text-muted d-block">Email</small>
                            <span class="fw-bold"><?= htmlspecialchars($user['loginEmail'] ?? '') ?></span>
                        </div>
                        <div class="list-group-item">
                            <small class="text-muted d-block">Должность</small>
                            <span class="fw-bold"><?= htmlspecialchars($user['jobTitle'] ?? '') ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-8 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Изменение данных профиля</h5>
                </div>
                <div class="card-body">
                    <form id="profileUpdateForm">
                        <div class="alert alert-warning d-none" id="profileUpdateMessage"></div>
                        
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="surName" class="form-label">Фамилия</label>
                                <input type="text" class="form-control" id="surName" name="surName" 
                                    value="<?= htmlspecialchars($user['surName'] ?? '') ?>">
                            </div>
                            <div class="col-md-4">
                                <label for="firstName" class="form-label">Имя</label>
                                <input type="text" class="form-control" id="firstName" name="firstName"
                                    value="<?= htmlspecialchars($user['firstName'] ?? '') ?>">
                            </div>
                            <div class="col-md-4">
                                <label for="secondName" class="form-label">Отчество</label>
                                <input type="text" class="form-control" id="secondName" name="secondName"
                                    value="<?= htmlspecialchars($user['secondName'] ?? '') ?>">
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="jobTitle" class="form-label">Должность</label>
                            <input type="text" class="form-control" id="jobTitle" name="jobTitle"
                                value="<?= htmlspecialchars($user['jobTitle'] ?? '') ?>">
                        </div>
                        
                        <div class="mb-3">
                            <label for="loginEmail" class="form-label">Email</label>
                            <input type="email" class="form-control" id="loginEmail" name="loginEmail"
                                value="<?= htmlspecialchars($user['loginEmail'] ?? '') ?>" readonly>
                        </div>
                        
                        <div class="mb-3">
                            <label for="currentPassword" class="form-label">Текущий пароль</label>
                            <input type="password" class="form-control" id="currentPassword" name="currentPassword">
                            <small class="form-text text-muted">Введите ваш текущий пароль для подтверждения изменений</small>
                        </div>
                        
                        <hr>
                        
                        <div class="mb-3">
                            <label for="newPassword" class="form-label">Новый пароль</label>
                            <input type="password" class="form-control" id="newPassword" name="newPassword">
                            <small class="form-text text-muted">Оставьте пустым, если не хотите менять</small>
                        </div>
                        
                        <div class="mb-3">
                            <label for="confirmPassword" class="form-label">Подтверждение нового пароля</label>
                            <input type="password" class="form-control" id="confirmPassword" name="confirmPassword">
                        </div>
                        
                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <button type="submit" class="btn btn-primary">Сохранить изменения</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    $('#profileUpdateForm').submit(function(e) {
        e.preventDefault();
        
        var formData = {
            'surName': $('#surName').val(),
            'firstName': $('#firstName').val(),
            'secondName': $('#secondName').val(),
            'jobTitle': $('#jobTitle').val(),
            'currentPassword': $('#currentPassword').val(),
            'newPassword': $('#newPassword').val(),
            'confirmPassword': $('#confirmPassword').val()
        };
        
        $.ajax({
            type: 'POST',
            url: '/profile/update',
            data: formData,
            dataType: 'json',
            encode: true,
            success: function(data) {
                if (data.status === 200) {
                    $('#profileUpdateMessage').removeClass('d-none alert-warning').addClass('alert-success').text(data.message);
                    setTimeout(function() {
                        location.reload();
                    }, 2000);
                } else {
                    $('#profileUpdateMessage').removeClass('d-none alert-success').addClass('alert-warning').text(data.message);
                }
            },
            error: function() {
                $('#profileUpdateMessage').removeClass('d-none').addClass('alert-danger').text('Произошла ошибка при отправке запроса');
            }
        });
    });
});
</script> 