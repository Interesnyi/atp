<div class="modal fade" id="registerModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
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
                        <label for="surName">Фамилия</label>
                        <input type="text" name="surName" id="surName" class="form-control" />
                    </div>
                    
                    <div class="mb-3">
                        <label for="firstName">Имя</label>
                        <input type="text" name="firstName" id="firstName" class="form-control" />
                    </div>
                    
                    <div class="mb-3">
                        <label for="secondName">Отчество</label>
                        <input type="text" name="secondName" id="secondName" class="form-control" />
                    </div>
                    
                    <div class="mb-3">
                        <label for="jobTitle">Должность</label>
                        <input type="text" name="jobTitle" id="jobTitle" class="form-control" />
                    </div>
                    
                    <div class="mb-3">
                        <label for="regLoginEmail">Логин (e-mail)</label>
                        <input type="text" name="loginEmail" id="regLoginEmail" class="form-control" />
                    </div>
                    
                    <div class="mb-3">
                        <label for="regPassword">Пароль</label>
                        <input type="password" name="password" id="regPassword" class="form-control" />
                    </div>
                    
                    <div class="mb-3">
                        <label for="passwordRepeat">Повторите пароль</label>
                        <input type="password" name="passwordRepeat" id="passwordRepeat" class="form-control" />
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Закрыть</button>
                    <button type="submit" class="btn btn-primary">Зарегистрироваться</button>
                </div>
            </form>
        </div>
    </div>
</div> 