<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="container">
        <a class="navbar-brand" href="/">ELDIR</a>
        <?php if (isset($_SESSION['id'])): ?>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="/maslosklad">Склад</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/balance">Касса</a>
                    </li>
                </ul>
                <div class="navbar-text me-3">
                    <?= $this->escape($_SESSION['surname']) ?>
                </div>
                <a href="/logout" class="btn btn-outline-primary">Выйти</a>
            </div>
        <?php endif; ?>
    </div>
</nav> 