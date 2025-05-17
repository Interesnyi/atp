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
                        <label for="loginEmail">Логин (e-mail)</label>
                        <input type="text" name="loginEmail" id="loginEmail" class="form-control" />
                    </div>
                    
                    <div class="mb-3">
                        <label for="password">Пароль</label>
                        <input type="password" name="password" id="password" class="form-control" />
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