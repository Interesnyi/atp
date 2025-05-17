<div class="row">
    <div class="col-md-12">
        <h1>ELDIR | Система управления организацией</h1>
    </div>
</div>

<?php if (!isset($_SESSION['id'])): ?>
    <div class="row">
        <div class="col-md-12">
            <div class="btn-group mt-3">
                <a href="/login" class="btn btn-primary">Войти</a>
                <a href="/register" class="btn btn-primary">Зарегистрироваться</a>
            </div>
        </div>
    </div>
<?php else: ?>
    <div class="row mt-3">
        <div class="col-md-12">
            <div class="alert alert-success">
                Добро пожаловать, <?= !empty($_SESSION['surname']) ? htmlspecialchars($_SESSION['surname']) : htmlspecialchars($_SESSION['loginemail']) ?>!
                <a href="/logout" class="btn btn-sm btn-outline-danger float-end">Выйти</a>
            </div>
        </div>
    </div>
<?php endif; ?> 