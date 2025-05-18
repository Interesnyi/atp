<div class="row">
    <div class="col-md-6 offset-md-3 text-center mt-5">
        <div class="card">
            <div class="card-body">
                <h1 class="display-1 text-danger">403</h1>
                <h2>Доступ запрещен</h2>
                <p class="lead"><?= isset($message) ? htmlspecialchars($message) : 'У вас нет прав для доступа к этой странице.' ?></p>
                <hr>
                <p>Если вы считаете, что это ошибка, обратитесь к администратору системы.</p>
                <a href="/" class="btn btn-primary">Вернуться на главную</a>
            </div>
        </div>
    </div>
</div> 