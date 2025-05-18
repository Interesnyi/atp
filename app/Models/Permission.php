<?php

namespace App\Models;

use App\Core\Model;

class Permission extends Model {
    protected $table = 'permissions';
    
    /**
     * Обработка текстовых полей из базы данных
     * Метод больше не нужен, но оставлен для совместимости
     */
    private function processTextFields($data) {
        // Просто возвращаем данные без конвертации,
        // т.к. база данных уже настроена на правильную кодировку
        return $data;
    }
    
    /**
     * Получение всех прав доступа с группировкой
     */
    public function getAllPermissions() {
        $sql = "SELECT p.*, pg.name as group_name 
                FROM {$this->table} p
                JOIN permission_groups pg ON p.group_id = pg.id
                ORDER BY pg.id, p.id";
        $result = $this->db->fetchAll($sql);
        return $this->processTextFields($result);
    }
    
    /**
     * Получение прав по роли
     */
    public function getPermissionsByRole($role) {
        $sql = "SELECT p.* 
                FROM {$this->table} p
                JOIN role_permissions rp ON p.id = rp.permission_id
                WHERE rp.role = ?
                ORDER BY p.id";
        $result = $this->db->fetchAll($sql, [$role]);
        return $this->processTextFields($result);
    }
    
    /**
     * Проверка наличия права у роли
     */
    public function hasPermission($role, $slug) {
        $sql = "SELECT COUNT(*) as count
                FROM role_permissions rp
                JOIN {$this->table} p ON rp.permission_id = p.id
                WHERE rp.role = ? AND p.slug = ?";
        $result = $this->db->fetch($sql, [$role, $slug]);
        return ($result && $result['count'] > 0);
    }
    
    /**
     * Получение всех групп прав
     */
    public function getAllGroups() {
        $sql = "SELECT * FROM permission_groups ORDER BY id";
        $result = $this->db->fetchAll($sql);
        return $this->processTextFields($result);
    }
    
    /**
     * Получение прав для конкретной группы
     */
    public function getPermissionsByGroup($groupId) {
        $result = $this->where('group_id = ?', [$groupId]);
        return $this->processTextFields($result);
    }
    
    /**
     * Назначение права для роли
     */
    public function assignPermission($role, $permissionId) {
        $sql = "INSERT IGNORE INTO role_permissions (role, permission_id) VALUES (?, ?)";
        return $this->db->execute($sql, [$role, $permissionId]);
    }
    
    /**
     * Отзыв права у роли
     */
    public function revokePermission($role, $permissionId) {
        $sql = "DELETE FROM role_permissions WHERE role = ? AND permission_id = ?";
        return $this->db->execute($sql, [$role, $permissionId]);
    }
    
    /**
     * Получение всех прав с информацией о назначении для роли
     */
    public function getPermissionsWithRoleInfo($role) {
        $sql = "SELECT p.*, pg.name as group_name,
                    CASE WHEN rp.role IS NOT NULL THEN 1 ELSE 0 END as assigned
                FROM {$this->table} p
                JOIN permission_groups pg ON p.group_id = pg.id
                LEFT JOIN role_permissions rp ON p.id = rp.permission_id AND rp.role = ?
                ORDER BY pg.id, p.id";
        $result = $this->db->fetchAll($sql, [$role]);
        return $this->processTextFields($result);
    }
    
    /**
     * Обновление всех прав для роли
     */
    public function updateRolePermissions($role, $permissionIds) {
        // Транзакция
        $this->db->beginTransaction();
        
        try {
            // Удаляем все текущие права для роли
            $sql = "DELETE FROM role_permissions WHERE role = ?";
            $this->db->execute($sql, [$role]);
            
            // Добавляем новые права
            if (!empty($permissionIds)) {
                $values = [];
                $placeholders = [];
                
                foreach ($permissionIds as $id) {
                    $placeholders[] = "(?, ?)";
                    $values[] = $role;
                    $values[] = $id;
                }
                
                $sql = "INSERT INTO role_permissions (role, permission_id) VALUES " . implode(", ", $placeholders);
                $this->db->execute($sql, $values);
            }
            
            $this->db->commit();
            return true;
        } catch (\Exception $e) {
            $this->db->rollback();
            error_log("Error in updateRolePermissions: " . $e->getMessage());
            return false;
        }
    }
} 