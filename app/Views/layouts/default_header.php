<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($title) ? htmlspecialchars($title, ENT_QUOTES, 'UTF-8') . ' | ' : '' ?>ELDIR</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    
    <style>
        body {
            background-color: #f8f9fa;
            font-family: Arial, sans-serif;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        
        .navbar {
            background-color: #343a40;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .card {
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        
        .card-header {
            font-weight: bold;
        }
        
        .content {
            flex: 1 0 auto;
            padding: 20px 0;
        }
        
        .bg-gradient-primary {
            background: linear-gradient(45deg, #007bff, #0056b3);
        }
        
        .bg-gradient-info {
            background: linear-gradient(45deg, #17a2b8, #117a8b);
        }
        
        .bg-gradient-warning {
            background: linear-gradient(45deg, #ffc107, #d39e00);
        }
        
        .bg-gradient-success {
            background: linear-gradient(45deg, #28a745, #1e7e34);
        }
        
        .bg-gradient-secondary {
            background: linear-gradient(45deg, #6c757d, #545b62);
        }
        
        footer {
            flex-shrink: 0;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark mb-4">
        <div class="container">
            <a class="navbar-brand" href="/">ELDIR</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="/dashboard">
                            <i class="fas fa-tachometer-alt"></i> Панель управления
                        </a>
                    </li>
                    
                    <!-- Раздел Складов -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="warehouseDropdown" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-warehouse"></i> Склады
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
                    
                    <li class="nav-item">
                        <a class="nav-link" href="/invoices">
                            <i class="fas fa-file-invoice"></i> Счета
                        </a>
                    </li>
                    
                    <li class="nav-item">
                        <a class="nav-link" href="/balance">
                            <i class="fas fa-money-bill-wave"></i> Финансы
                        </a>
                    </li>
                    
                    <li class="nav-item">
                        <a class="nav-link" href="/users">
                            <i class="fas fa-users"></i> Пользователи
                        </a>
                    </li>
                </ul>
                <ul class="navbar-nav">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-user-circle"></i> 
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
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                            <li><a class="dropdown-item" href="/profile"><i class="fas fa-user-cog"></i> Профиль</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="/logout"><i class="fas fa-sign-out-alt"></i> Выйти</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    
    <div class="content">
        <div class="container"><?php // Основной контейнер контента ?> 