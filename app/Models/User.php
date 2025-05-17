<?php

namespace App\Models;

use App\Core\Model;

class User extends Model {
    protected $table = 'users';

    public function findByEmail($email) {
        return $this->findOne('loginEmail = ?', [$email]);
    }

    public function validateCredentials($email, $password) {
        $user = $this->findByEmail($email);
        if (!$user) {
            return false;
        }
        return $user['password'] === $password;
    }
    
    public function getTableStructure() {
        $sql = "DESCRIBE {$this->table}";
        return $this->db->fetchAll($sql);
    }
} 