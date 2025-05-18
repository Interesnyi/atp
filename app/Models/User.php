<?php

namespace App\Models;

use App\Core\Model;

class User extends Model {
    protected $table = 'users';

    /**
     * Поиск пользователя по email
     */
    public function findByEmail($email) {
        // Проверяем как по новому полю email, так и по старому loginEmail
        $user = $this->findOne('email = ? OR loginEmail = ?', [$email, $email]);
        return $user;
    }

    /**
     * Валидация учетных данных пользователя
     */
    public function validateCredentials($email, $password) {
        $user = $this->findByEmail($email);
        if (!$user) {
            return false;
        }
        
        // Используем безопасное сравнение паролей с учетом хеширования
        if (password_verify($password, $user['password'])) {
            // Проверяем и заполняем поля для совместимости
            if (empty($user['email']) && !empty($user['loginEmail'])) {
                $user['email'] = $user['loginEmail'];
            }
            
            if (empty($user['username']) && !empty($user['surName'])) {
                $user['username'] = $user['surName'] . ' ' . $user['firstName'];
            }
            
            return $user;
        }
        
        return false;
    }
    
    /**
     * Получение структуры таблицы пользователей
     */
    public function getTableStructure() {
        $sql = "DESCRIBE {$this->table}";
        return $this->db->fetchAll($sql);
    }
    
    /**
     * Получение списка всех пользователей
     */
    public function getAllUsers($limit = 100, $offset = 0) {
        try {
            // Отладочная информация
            error_log("DEBUG: User->getAllUsers вызван с параметрами limit=$limit, offset=$offset");
            
            $sql = "SELECT * FROM {$this->table} ORDER BY id DESC LIMIT ? OFFSET ?";
            
            error_log("DEBUG: SQL запрос: $sql");
            
            $result = $this->db->fetchAll($sql, [$limit, $offset]);
            
            error_log("DEBUG: Количество полученных пользователей: " . count($result));
            
            // Выводим первую запись для проверки структуры
            if (count($result) > 0) {
                error_log("DEBUG: Первая запись: " . print_r($result[0], true));
            }
            
            return $result;
        } catch (\Exception $e) {
            error_log("ОШИБКА в User->getAllUsers: " . $e->getMessage());
            error_log("Трассировка: " . $e->getTraceAsString());
            throw $e; // Пробрасываем исключение дальше для обработки в контроллере
        }
    }
    
    /**
     * Получение пользователя по ID
     */
    public function getUserById($id) {
        return $this->find($id);
    }
    
    /**
     * Создание нового пользователя
     */
    public function createUser($data) {
        return $this->create([
            'username' => $data['username'],
            'email' => $data['email'],
            'loginEmail' => $data['email'], // Для совместимости
            'firstName' => $data['username'], // Для совместимости
            'surName' => '', // Для совместимости
            'secondName' => '', // Для совместимости
            'password' => $data['password'],
            'role' => $data['role'] ?? 'user',
            'created_at' => date('Y-m-d H:i:s')
        ]);
    }
    
    /**
     * Обновление данных пользователя
     */
    public function updateUser($id, $data) {
        $updateData = [
            'username' => $data['username'],
            'email' => $data['email'],
            'updated_at' => date('Y-m-d H:i:s')
        ];
        
        // Если указан пароль, обновляем его
        if (isset($data['password'])) {
            $updateData['password'] = $data['password'];
        }
        
        if (isset($data['role'])) {
            $updateData['role'] = $data['role'];
        }
        
        return $this->update($id, $updateData);
    }
    
    /**
     * Удаление пользователя
     */
    public function deleteUser($id) {
        return $this->delete($id);
    }
    
    /**
     * Поиск пользователей с фильтрацией
     */
    public function searchUsers($query, $limit = 100, $offset = 0) {
        $query = "%{$query}%";
        $sql = "SELECT * FROM {$this->table} 
                WHERE username LIKE ? OR email LIKE ? OR loginEmail LIKE ?
                ORDER BY id DESC LIMIT ? OFFSET ?";
                
        return $this->db->fetchAll($sql, [$query, $query, $query, $limit, $offset]);
    }
    
    /**
     * Проверка наличия пользователя с указанной ролью
     */
    public function hasUserWithRole($role) {
        $sql = "SELECT COUNT(*) as count FROM {$this->table} WHERE role = ?";
        $result = $this->db->fetchOne($sql, [$role]);
        return $result['count'] > 0;
    }
    
    /**
     * Получение общего количества пользователей
     */
    public function getTotalUsers() {
        $sql = "SELECT COUNT(*) as count FROM {$this->table}";
        $result = $this->db->fetchOne($sql);
        return $result['count'];
    }
} 