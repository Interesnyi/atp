<?php

namespace App\Controllers;

use App\Core\Controller;

class ProfileController extends Controller {
    private $user;

    public function __construct() {
        parent::__construct();
        // Проверка авторизации
        $this->checkAuth();
        $this->user = $this->loadModel('User');
    }
    
    private function checkAuth() {
        if (!isset($_SESSION['id'])) {
            $this->redirect('/login');
        }
    }

    public function index() {
        // Получаем данные текущего пользователя
        $userId = $_SESSION['id'];
        $user = $this->user->find($userId);
        
        if (!$user) {
            // Если пользователь не найден, выход из системы
            session_destroy();
            $this->redirect('/login');
        }
        
        return $this->view('profile/index', ['user' => $user]);
    }
    
    public function update() {
        if (!$this->isPost()) {
            return $this->json(['status' => 400, 'message' => 'Bad Request']);
        }
        
        $data = $this->getPost();
        $userId = $_SESSION['id'];
        $user = $this->user->find($userId);
        
        if (!$user) {
            return $this->json(['status' => 404, 'message' => 'Пользователь не найден']);
        }
        
        // Проверяем текущий пароль для авторизации действия
        if (empty($data['currentPassword']) || $user['password'] !== $data['currentPassword']) {
            return $this->json(['status' => 422, 'message' => 'Неверный текущий пароль']);
        }
        
        // Готовим данные для обновления
        $userData = [
            'surName' => $data['surName'],
            'firstName' => $data['firstName'],
            'secondName' => $data['secondName'],
            'jobTitle' => $data['jobTitle']
        ];
        
        // Если указан новый пароль, проверяем его
        if (!empty($data['newPassword'])) {
            if ($data['newPassword'] !== $data['confirmPassword']) {
                return $this->json(['status' => 422, 'message' => 'Пароли не совпадают']);
            }
            $userData['password'] = $data['newPassword'];
        }
        
        try {
            $this->user->update($userId, $userData);
            
            // Обновляем сессию
            $_SESSION['surname'] = $userData['surName'];
            
            return $this->json([
                'status' => 200,
                'message' => 'Профиль успешно обновлен'
            ]);
        } catch (\Exception $e) {
            return $this->json([
                'status' => 500,
                'message' => 'Ошибка при обновлении профиля: ' . $e->getMessage()
            ]);
        }
    }
} 