<?php

namespace App\Models;

use App\Core\Model;

class User extends Model {
    protected $table = 'users';

    /**
     * Поиск пользователя по email
     */
    public function findByEmail($email) {
        return $this->findOne('email = ?', [$email]);
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
        $sql = "SELECT * FROM {$this->table} ORDER BY id DESC LIMIT ? OFFSET ?";
        return $this->db->fetchAll($sql, [$limit, $offset]);
    }
    
    /**
     * Получение пользователя по ID
     */
    public function getUserById($id) {
        return $this->findById($id);
    }
    
    /**
     * Создание нового пользователя
     */
    public function createUser($data) {
        return $this->create([
            'username' => $data['username'],
            'email' => $data['email'],
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
            'role' => $data['role'] ?? 'user',
            'updated_at' => date('Y-m-d H:i:s')
        ];
        
        // Если указан пароль, обновляем его
        if (isset($data['password'])) {
            $updateData['password'] = $data['password'];
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
                WHERE username LIKE ? OR email LIKE ? 
                ORDER BY id DESC LIMIT ? OFFSET ?";
                
        return $this->db->fetchAll($sql, [$query, $query, $limit, $offset]);
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