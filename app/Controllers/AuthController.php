<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\User;

class AuthController extends Controller {
    private $user;

    public function __construct() {
        parent::__construct();
        $this->user = $this->loadModel('User');
    }

    public function index() {
        // Если пользователь уже авторизован, перенаправляем на дашборд
        if (isset($_SESSION['id'])) {
            $this->redirect('/dashboard');
        }
        
        return $this->view('auth/login');
    }

    public function registerForm() {
        // Если пользователь уже авторизован, перенаправляем на дашборд
        if (isset($_SESSION['id'])) {
            $this->redirect('/dashboard');
        }
        
        return $this->view('auth/register');
    }

    public function login() {
        if (!$this->isPost()) {
            return $this->json(['status' => 400, 'message' => 'Bad Request']);
        }

        $loginEmail = $this->getPost('loginEmail');
        $password = $this->getPost('password');

        // Дебаг: Вывод входных данных
        error_log("Login attempt: Email = {$loginEmail}");

        if (!$this->validateRequired($this->getPost(), ['loginEmail', 'password'])) {
            return $this->json([
                'status' => 422,
                'message' => 'Все поля обязательны для заполнения'
            ]);
        }

        // Дебаг: Проверка имени таблицы
        error_log("User table: " . $this->user->getTableName());

        // Дебаг: Формируем SQL запрос для проверки
        $sql = "SELECT * FROM {$this->user->getTableName()} WHERE loginEmail = ?";
        error_log("SQL Query: {$sql}");
        
        $user = $this->user->findOne("loginEmail = ?", [$loginEmail]);

        // Дебаг: Результат запроса
        error_log("User found: " . ($user ? 'Yes' : 'No'));
        if ($user) {
            error_log("User data: " . json_encode($user));
            error_log("Password comparison: Input = '{$password}', DB = '{$user['password']}', Match = " . 
                      ($user['password'] === $password ? 'Yes' : 'No'));
        }

        if (!$user || $user['password'] !== $password) {
            return $this->json([
                'status' => 422,
                'message' => 'Неверный логин или пароль'
            ]);
        }

        // Сохраняем данные в сессии, проверяя наличие ключей
        $_SESSION['id'] = $user['id'];
        $_SESSION['loginemail'] = $user['loginEmail'];
        $_SESSION['surname'] = isset($user['surName']) ? $user['surName'] : '';

        return $this->json([
            'status' => 200,
            'message' => 'Авторизация успешна',
            'redirect' => '/dashboard'
        ]);
    }

    public function register() {
        if (!$this->isPost()) {
            return $this->json(['status' => 400, 'message' => 'Bad Request']);
        }

        $data = $this->getPost();
        
        // Дебаг: Вывод полученных данных
        error_log("Register attempt: " . json_encode($data));
        
        $required = ['surName', 'firstName', 'secondName', 'jobTitle', 'loginEmail', 'password', 'passwordRepeat'];

        if (!$this->validateRequired($data, $required)) {
            return $this->json([
                'status' => 422,
                'message' => 'Все поля обязательны для заполнения'
            ]);
        }

        if ($data['password'] !== $data['passwordRepeat']) {
            return $this->json([
                'status' => 422,
                'message' => 'Пароли не совпадают'
            ]);
        }

        $existingUser = $this->user->findOne("loginEmail = ?", [$data['loginEmail']]);
        if ($existingUser) {
            return $this->json([
                'status' => 422,
                'message' => 'Пользователь с таким email уже существует'
            ]);
        }

        // Проверяем структуру таблицы
        try {
            $tableInfo = $this->user->getTableStructure();
            error_log("Table structure: " . json_encode($tableInfo));
        } catch (\Exception $e) {
            error_log("Error getting table structure: " . $e->getMessage());
        }

        $userData = [
            'surName' => $data['surName'],
            'firstName' => $data['firstName'],
            'secondName' => $data['secondName'],
            'jobTitle' => $data['jobTitle'],
            'loginEmail' => $data['loginEmail'],
            'password' => $data['password']
        ];

        // Дебаг: Вывод данных для создания пользователя
        error_log("User data for creation: " . json_encode($userData));

        try {
            $userId = $this->user->create($userData);
            error_log("User created with ID: " . $userId);
            
            return $this->json([
                'status' => 200,
                'message' => 'Регистрация успешна'
            ]);
        } catch (\Exception $e) {
            error_log("Registration error: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            
            return $this->json([
                'status' => 500,
                'message' => 'Ошибка при регистрации: ' . $e->getMessage()
            ]);
        }
    }

    public function logout() {
        session_destroy();
        $this->redirect('/');
    }
} 