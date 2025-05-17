<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\User;

class UsersController extends Controller {
    private $userModel;

    public function __construct() {
        parent::__construct();
        // Проверяем авторизацию для всех методов, кроме специальных
        $this->middleware('auth');
        $this->userModel = new User();
    }

    /**
     * Отображение списка пользователей
     */
    public function index() {
        // Получаем пользователей с пагинацией
        $users = $this->userModel->getAllUsers();
        
        $this->view->render('users/index', [
            'title' => 'Пользователи',
            'users' => $users
        ]);
    }

    /**
     * Отображение формы создания пользователя
     */
    public function create() {
        $this->view->render('users/create', [
            'title' => 'Создание пользователя'
        ]);
    }

    /**
     * Сохранение нового пользователя
     */
    public function store() {
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
            $this->view->render('users/create', [
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
        
        if ($this->userModel->createUser($data)) {
            // Редирект на список с сообщением об успехе
            $_SESSION['success'] = 'Пользователь успешно создан';
            header('Location: /users');
            exit;
        } else {
            $errors[] = 'Ошибка создания пользователя';
            $this->view->render('users/create', [
                'title' => 'Создание пользователя',
                'errors' => $errors,
                'username' => $username,
                'email' => $email,
                'role' => $role
            ]);
        }
    }

    /**
     * Отображение данных пользователя
     */
    public function show($params) {
        $userId = $params['id'] ?? 0;
        $user = $this->userModel->getUserById($userId);
        
        if (!$user) {
            $this->renderNotFound('Пользователь не найден');
            return;
        }
        
        $this->view->render('users/show', [
            'title' => 'Профиль пользователя',
            'user' => $user
        ]);
    }

    /**
     * Отображение формы редактирования
     */
    public function edit($params) {
        $userId = $params['id'] ?? 0;
        $user = $this->userModel->getUserById($userId);
        
        if (!$user) {
            $this->renderNotFound('Пользователь не найден');
            return;
        }
        
        $this->view->render('users/edit', [
            'title' => 'Редактирование пользователя',
            'user' => $user
        ]);
    }

    /**
     * Обновление данных пользователя
     */
    public function update($params) {
        $userId = $params['id'] ?? 0;
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
            $this->view->render('users/edit', [
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
        
        if ($this->userModel->updateUser($userId, $data)) {
            $_SESSION['success'] = 'Данные пользователя обновлены';
            header('Location: /users');
            exit;
        } else {
            $errors[] = 'Ошибка обновления пользователя';
            $this->view->render('users/edit', [
                'title' => 'Редактирование пользователя',
                'user' => $user,
                'errors' => $errors
            ]);
        }
    }

    /**
     * Удаление пользователя
     */
    public function delete($params) {
        $userId = $params['id'] ?? 0;
        
        // Проверяем, существует ли пользователь
        if (!$this->userModel->getUserById($userId)) {
            $_SESSION['error'] = 'Пользователь не найден';
            header('Location: /users');
            exit;
        }
        
        // Запрещаем удаление текущего пользователя
        if ($userId == $_SESSION['user_id']) {
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