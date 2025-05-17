<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ELDIR - Система управления организацией</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css" rel="stylesheet">
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        body {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        .content {
            flex: 1;
            padding-bottom: 30px;
        }
        .footer {
            background-color: #f8f9fa;
            padding: 15px 0;
            font-size: 0.9rem;
            border-top: 1px solid #dee2e6;
        }
    </style>
</head>
<body>
    <!-- Навигационная панель -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container">
            <a class="navbar-brand" href="/">ELDIR</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <?php if (isset($_SESSION['id'])): ?>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="/dashboard">
                            <i class="bi bi-speedometer2"></i> Панель управления
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/balance">
                            <i class="bi bi-cash-stack"></i> Финансы
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/maslosklad">
                            <i class="bi bi-boxes"></i> Склад масел
                        </a>
                    </li>
                </ul>
                <ul class="navbar-nav">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-person-circle"></i> 
                            <?= !empty($_SESSION['surname']) ? htmlspecialchars($_SESSION['surname']) : htmlspecialchars($_SESSION['loginemail']) ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                            <li><a class="dropdown-item" href="/profile"><i class="bi bi-gear"></i> Профиль</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="/logout"><i class="bi bi-box-arrow-right"></i> Выйти</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
            <?php endif; ?>
        </div>
    </nav>

    <!-- Основное содержимое -->
    <div class="content container">
        <?php echo $content; ?>
    </div>

    <!-- Подвал -->
    <footer class="footer mt-auto">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <span>&copy; <?= date('Y') ?> ELDIR. Все права защищены.</span>
                </div>
                <div class="col-md-6 text-end">
                    <span>Версия 1.0.0</span>
                </div>
            </div>
        </div>
    </footer>
</body>
</html> 