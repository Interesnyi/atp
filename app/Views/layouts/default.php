<!DOCTYPE html>
<html lang="ru">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($title) ? htmlspecialchars($title, ENT_QUOTES, 'UTF-8') . ' | ' : '' ?>ELDIR</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css" rel="stylesheet">
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        :root {
            --primary-color: #3d5a80;
            --secondary-color: #98c1d9;
            --accent-color: #ee6c4d;
            --light-bg: #f7f7f9;
            --dark-text: #293241;
        }
        
        body {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: var(--dark-text);
            background-color: var(--light-bg);
        }
        
        .navbar {
            background-color: var(--primary-color) !important;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .navbar-brand, .navbar-nav .nav-link {
            color: white !important;
        }
        
        .navbar-nav .nav-link:hover {
            color: var(--secondary-color) !important;
        }
        
        .navbar-toggler {
            border-color: rgba(255,255,255,0.5);
        }
        
        .navbar-toggler-icon {
            filter: brightness(0) invert(1);
        }
        
        .content {
            flex: 1;
            padding: 30px 0;
        }
        
        .card {
            border: none;
            border-radius: 8px;
            box-shadow: 0 3px 6px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        
        .card-header {
            background-color: white;
            border-bottom: 1px solid rgba(0,0,0,0.05);
            padding: 15px 20px;
        }
        
        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }
        
        .btn-primary:hover, .btn-primary:focus {
            background-color: #2d4a70;
            border-color: #2d4a70;
        }
        
        .table thead th {
            border-top: none;
            border-bottom-width: 1px;
            background-color: rgba(0,0,0,0.02);
            font-weight: 600;
        }
        
        .footer {
            background-color: var(--primary-color);
            color: white;
            padding: 15px 0;
            font-size: 0.9rem;
            border-top: 1px solid rgba(0,0,0,0.05);
        }
        
        .dropdown-menu {
            border: none;
            box-shadow: 0 3px 12px rgba(0,0,0,0.15);
        }
    </style>
</head>
<body>
    <!-- Навигационная панель -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="/">ELDIR</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <?php if (isset($_SESSION['user_id']) || isset($_SESSION['id'])): ?>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <?php // Проверки на доступ к разным разделам ?>
                    
                    <?php $role = $_SESSION['role'] ?? 'user'; ?>
                    
                    <?php // Раздел "Панель управления" ?>
                    <?php if ($role === 'admin' || $role === 'manager' || isset($userPermissions['dashboard.access'])): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="/dashboard">
                            <i class="bi bi-speedometer2"></i> Панель управления
                        </a>
                    </li>
                    <?php endif; ?>
                    
                    <?php // Раздел "Финансы" ?>
                    <?php if ($role === 'admin' || $role === 'manager' || isset($userPermissions['finance.view'])): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="/balance">
                            <i class="bi bi-cash-stack"></i> Финансы
                        </a>
                    </li>
                    <?php endif; ?>
                    
                    <?php // Раздел "Склады" (dropdown вместо "Склад масел") ?>
                    <?php if ($role === 'admin' || $role === 'manager' || isset($userPermissions['storage.view'])): ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="warehouseDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-boxes"></i> Склады
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="warehouseDropdown">
                            <li><a class="dropdown-item" href="/warehouses">Все склады</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="/warehouses/material">Материальный склад</a></li>
                            <li><a class="dropdown-item" href="/warehouses/tool">Инструментальный склад</a></li>
                            <li><a class="dropdown-item" href="/warehouses/oil">Склад ГСМ</a></li>
                            <li><a class="dropdown-item" href="/warehouses/autoparts">Склад автозапчастей</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="/warehouses/manage">Управление складами</a></li>
                        </ul>
                    </li>
                    <?php endif; ?>
                    
                    <?php // Раздел "Пользователи" ?>
                    <?php if ($role === 'admin' || isset($userPermissions['users.view'])): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="/users">
                            <i class="bi bi-people"></i> Пользователи
                        </a>
                    </li>
                    <?php endif; ?>
                    
                    <?php // Раздел "Управление ролями" - только для администраторов ?>
                    <?php if ($role === 'admin'): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="/roles">
                            <i class="bi bi-shield-lock"></i> Управление ролями
                        </a>
                    </li>
                    <?php endif; ?>
                </ul>
                <ul class="navbar-nav">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-person-circle"></i> 
                            <?php 
                            if (!empty($_SESSION['username'])) {
                                echo htmlspecialchars($_SESSION['username']);
                            } elseif (!empty($_SESSION['surname'])) {
                                echo htmlspecialchars($_SESSION['surname']);
                            } elseif (!empty($_SESSION['email'])) {
                                echo htmlspecialchars($_SESSION['email']);
                            } elseif (!empty($_SESSION['loginemail'])) {
                                echo htmlspecialchars($_SESSION['loginemail']);
                            } else {
                                echo 'Пользователь';
                            }
                            ?>
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