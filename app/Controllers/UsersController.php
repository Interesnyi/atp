<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\User;

class UsersController extends Controller {
    private $userModel;

    public function __construct() {
        parent::__construct();
        // Проверяем авторизацию для всех методов
        $this->middleware('auth');
        // Доступ только с правом просмотра пользователей
        $this->middleware('permission', 'users.view');
        $this->userModel = new User();
    }

    /**
     * Отображение списка пользователей
     */
    public function index() {
        try {
            // Получаем пользователей с пагинацией
            $users = $this->userModel->getAllUsers();
            
            // Включаем отображение всех ошибок для отладки
            ini_set('display_errors', 1);
            ini_set('display_startup_errors', 1);
            error_reporting(E_ALL);
            
            error_log("DEBUG UsersController->index: Перед вызовом view");
            
            // Стандартное отображение через механизм шаблонов
            $this->view('users/index', [
                'title' => 'Пользователи',
                'users' => $users,
                'canCreate' => $this->hasPermission('users.create'),
                'canEdit' => $this->hasPermission('users.edit'),
                'canDelete' => $this->hasPermission('users.delete')
            ]);
            
        } catch (\Exception $e) {
            echo "<div style='color:red; padding:20px; border:1px solid red;'>";
            echo "<h2>Ошибка в UsersController->index</h2>";
            echo "<p>" . $e->getMessage() . "</p>";
            echo "<pre>" . $e->getTraceAsString() . "</pre>";
            echo "</div>";
            exit;
        }
    }

    /**
     * Отображение формы создания пользователя
     */
    public function create() {
        // Проверка прав на создание пользователей
        $this->middleware('permission', 'users.create');
        
        $this->view('users/create', [
            'title' => 'Создание пользователя'
        ]);
    }

    /**
     * Сохранение нового пользователя
     */
    public function store() {
        try {
            // Проверка прав на создание пользователей
            $this->middleware('permission', 'users.create');
            
            // Включаем отображение всех ошибок для отладки
            ini_set('display_errors', 1);
            ini_set('display_startup_errors', 1);
            error_reporting(E_ALL);
            
            // Отладка запроса
            error_log("DEBUG store: получены данные формы");
            
            // Валидация данных
            $errors = [];
            
            $username = $_POST['username'] ?? '';
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';
            $role = $_POST['role'] ?? 'user';
            
            // Проверки полей
            if (empty($username)) {
                $errors[] = 'Имя пользователя обязательно';
            }
            
            if (empty($email)) {
                $errors[] = 'Email обязателен';
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = 'Неверный формат email';
            }
            
            if (empty($password)) {
                $errors[] = 'Пароль обязателен';
            } elseif (strlen($password) < 6) {
                $errors[] = 'Пароль должен быть не менее 6 символов';
            }
            
            // Проверка существования пользователя
            if ($this->userModel->findByEmail($email)) {
                $errors[] = 'Пользователь с таким email уже существует';
            }
            
            if (!empty($errors)) {
                $this->view('users/create', [
                    'title' => 'Создание пользователя',
                    'errors' => $errors,
                    'username' => $username,
                    'email' => $email,
                    'role' => $role
                ]);
                return;
            }
            
            // Хеширование пароля и создание пользователя
            $data = [
                'username' => $username,
                'email' => $email,
                'password' => password_hash($password, PASSWORD_DEFAULT),
                'role' => $role
            ];
            
            error_log("DEBUG store: попытка создать пользователя с данными: " . json_encode($data));
            
            if ($this->userModel->createUser($data)) {
                // Редирект на список с сообщением об успехе
                $_SESSION['success'] = 'Пользователь успешно создан';
                header('Location: /users');
                exit;
            } else {
                $errors[] = 'Ошибка создания пользователя';
                $this->view('users/create', [
                    'title' => 'Создание пользователя',
                    'errors' => $errors,
                    'username' => $username,
                    'email' => $email,
                    'role' => $role
                ]);
            }
        } catch (\Exception $e) {
            // Отображаем детали ошибки
            echo "<div style='color:red; padding:20px; border:1px solid red;'>";
            echo "<h2>Ошибка в UsersController->store</h2>";
            echo "<p>" . $e->getMessage() . "</p>";
            echo "<pre>" . $e->getTraceAsString() . "</pre>";
            echo "</div>";
        }
    }

    /**
     * Отображение данных пользователя
     */
    public function show($id) {
        try {
            $this->middleware('permission', 'users.view');
            $userId = $id;
            $user = $this->userModel->getUserById($userId);
            if (!$user) {
                $this->renderNotFound('Пользователь не найден');
                return;
            }
            $this->view('users/show', [
                'title' => 'Профиль пользователя',
                'user' => $user,
                'canEdit' => $this->hasPermission('users.edit'),
                'canDelete' => $this->hasPermission('users.delete')
            ]);
        } catch (\Exception $e) {
            echo "<div style='color:red; padding:20px; border:1px solid red;'>";
            echo "<h2>Ошибка в UsersController->show</h2>";
            echo "<p>" . $e->getMessage() . "</p>";
            echo "<pre>" . $e->getTraceAsString() . "</pre>";
            echo "</div>";
        }
    }

    /**
     * Отображение формы редактирования
     */
    public function edit($id) {
        try {
            $this->middleware('permission', 'users.edit');
            $userId = $id;
            $user = $this->userModel->getUserById($userId);
            if (!$user) {
                $this->renderNotFound('Пользователь не найден');
                return;
            }
            $this->view('users/edit', [
                'title' => 'Редактирование пользователя',
                'user' => $user
            ]);
        } catch (\Exception $e) {
            echo "<div style='color:red; padding:20px; border:1px solid red;'>";
            echo "<h2>Ошибка в UsersController->edit</h2>";
            echo "<p>" . $e->getMessage() . "</p>";
            echo "<pre>" . $e->getTraceAsString() . "</pre>";
            echo "</div>";
        }
    }

    /**
     * Обновление данных пользователя
     */
    public function update($id) {
        try {
            // Проверка прав на редактирование пользователей
            $this->middleware('permission', 'users.edit');
            
            // Включаем отображение всех ошибок для отладки
            ini_set('display_errors', 1);
            ini_set('display_startup_errors', 1);
            error_reporting(E_ALL);
            
            $userId = $id;
            
            // Отладка запроса
            error_log("DEBUG update: получен ID пользователя: {$userId}");
            
            $user = $this->userModel->getUserById($userId);
            
            if (!$user) {
                $this->renderNotFound('Пользователь не найден');
                return;
            }
            
            // Валидация данных
            $errors = [];
            
            $username = $_POST['username'] ?? '';
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';
            $role = $_POST['role'] ?? 'user';
            
            if (empty($username)) {
                $errors[] = 'Имя пользователя обязательно';
            }
            
            if (empty($email)) {
                $errors[] = 'Email обязателен';
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = 'Неверный формат email';
            }
            
            // Проверка на дублирование email (кроме текущего пользователя)
            $existingUser = $this->userModel->findByEmail($email);
            if ($existingUser && $existingUser['id'] != $userId) {
                $errors[] = 'Пользователь с таким email уже существует';
            }
            
            if (!empty($errors)) {
                $this->view('users/edit', [
                    'title' => 'Редактирование пользователя',
                    'user' => $user,
                    'errors' => $errors
                ]);
                return;
            }
            
            // Подготовка данных для обновления
            $data = [
                'username' => $username,
                'email' => $email,
                'role' => $role
            ];
            
            // Если пароль указан, хешируем его
            if (!empty($password)) {
                $data['password'] = password_hash($password, PASSWORD_DEFAULT);
            }
            
            error_log("DEBUG update: попытка обновить пользователя {$userId} с данными: " . json_encode($data));
            
            if ($this->userModel->updateUser($userId, $data)) {
                $_SESSION['success'] = 'Данные пользователя обновлены';
                header('Location: /users');
                exit;
            } else {
                $errors[] = 'Ошибка обновления пользователя';
                $this->view('users/edit', [
                    'title' => 'Редактирование пользователя',
                    'user' => $user,
                    'errors' => $errors
                ]);
            }
        } catch (\Exception $e) {
            // Отображаем детали ошибки
            echo "<div style='color:red; padding:20px; border:1px solid red;'>";
            echo "<h2>Ошибка в UsersController->update</h2>";
            echo "<p>" . $e->getMessage() . "</p>";
            echo "<pre>" . $e->getTraceAsString() . "</pre>";
            echo "</div>";
        }
    }

    /**
     * Удаление пользователя
     */
    public function delete($id) {
        // Проверка прав на удаление пользователей
        $this->middleware('permission', 'users.delete');
        
        $userId = $id;
        
        // Проверяем, существует ли пользователь
        if (!$this->userModel->getUserById($userId)) {
            $_SESSION['error'] = 'Пользователь не найден';
            header('Location: /users');
            exit;
        }
        
        // Определяем ID текущего пользователя из сессии
        $currentUserId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : (isset($_SESSION['id']) ? $_SESSION['id'] : 0);
        
        // Запрещаем удаление текущего пользователя
        if ($userId == $currentUserId) {
            $_SESSION['error'] = 'Нельзя удалить свою учетную запись';
            header('Location: /users');
            exit;
        }
        
        if ($this->userModel->deleteUser($userId)) {
            $_SESSION['success'] = 'Пользователь удален';
        } else {
            $_SESSION['error'] = 'Ошибка удаления пользователя';
        }
        
        header('Location: /users');
        exit;
    }
} 