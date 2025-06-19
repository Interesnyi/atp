<div class="container mt-4">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Добавление нового пользователя</h5>
                </div>
                <div class="card-body">
                    <?php if (isset($errors) && !empty($errors)): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <strong>Ошибки при заполнении формы:</strong>
                            <ul class="mb-0 mt-2">
                                <?php foreach ($errors as $error): ?>
                                    <li><?= $error ?></li>
                                <?php endforeach; ?>
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>
                    
                    <form action="/users/store" method="POST" class="needs-validation" novalidate>
                        <div class="mb-3">
                            <label for="username" class="form-label">Имя пользователя <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="username" name="username" 
                                value="<?= isset($username) ? htmlspecialchars($username) : '' ?>" required>
                            <div class="invalid-feedback">
                                Пожалуйста, введите имя пользователя.
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" class="form-control" id="email" name="email" 
                                value="<?= isset($email) ? htmlspecialchars($email) : '' ?>" required>
                            <div class="invalid-feedback">
                                Пожалуйста, введите корректный email.
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="jobTitle" class="form-label">Должность <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="jobTitle" name="jobTitle" value="<?= isset(
                                $jobTitle) ? htmlspecialchars($jobTitle) : '' ?>" required>
                            <div class="invalid-feedback">
                                Пожалуйста, укажите должность.
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="password" class="form-label">Пароль <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="password" class="form-control" id="password" name="password" required>
                                <button class="btn btn-outline-secondary" type="button" id="togglePassword" title="Показать/скрыть пароль">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>
                            <div class="form-text">Пароль должен содержать не менее 6 символов.</div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="role" class="form-label">Роль пользователя</label>
                            <select class="form-select" id="role" name="role">
                                <option value="user" <?= (isset($role) && $role === 'user') ? 'selected' : '' ?>>Пользователь</option>
                                <option value="manager" <?= (isset($role) && $role === 'manager') ? 'selected' : '' ?>>Менеджер</option>
                                <option value="admin" <?= (isset($role) && $role === 'admin') ? 'selected' : '' ?>>Администратор</option>
                            </select>
                        </div>
                        
                        <div class="d-flex justify-content-between mt-4">
                            <a href="/users" class="btn btn-secondary">Отмена</a>
                            <button type="submit" class="btn btn-primary">Создать пользователя</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Валидация формы на стороне клиента
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.querySelector('.needs-validation');
        
        form.addEventListener('submit', function(event) {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            form.classList.add('was-validated');
        });
        
        // Переключение видимости пароля
        const togglePassword = document.querySelector('#togglePassword');
        const password = document.querySelector('#password');
        
        togglePassword.addEventListener('click', function() {
            const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
            password.setAttribute('type', type);
            
            // Изменение иконки
            this.querySelector('i').classList.toggle('bi-eye');
            this.querySelector('i').classList.toggle('bi-eye-slash');
        });
    });
</script> 