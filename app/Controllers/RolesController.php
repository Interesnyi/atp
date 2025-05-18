<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Permission;

class RolesController extends Controller {
    private $permissionModel;

    public function __construct() {
        parent::__construct();
        // Доступ только для администраторов
        $this->middleware('role', 'admin');
        $this->permissionModel = new Permission();
    }

    /**
     * Отображение списка ролей
     */
    public function index() {
        $roles = ['admin', 'manager', 'user'];
        
        $this->view('roles/index', [
            'title' => 'Управление ролями',
            'roles' => $roles
        ]);
    }

    /**
     * Отображение и управление правами для конкретной роли
     */
    public function permissions($params) {
        $role = $params['role'] ?? '';
        
        if (!in_array($role, ['admin', 'manager', 'user'])) {
            $this->renderNotFound('Указанная роль не найдена');
            return;
        }
        
        // При отправке формы
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $permissionIds = $_POST['permissions'] ?? [];
            
            if ($this->permissionModel->updateRolePermissions($role, $permissionIds)) {
                $_SESSION['success'] = 'Права для роли успешно обновлены';
            } else {
                $_SESSION['error'] = 'Ошибка при обновлении прав';
            }
            
            header('Location: /roles/permissions/' . $role);
            exit;
        }
        
        // Получаем все права с пометками о назначении для роли
        $permissions = $this->permissionModel->getPermissionsWithRoleInfo($role);
        
        // Группируем права по разделам
        $groupedPermissions = [];
        
        foreach ($permissions as $permission) {
            $groupName = $permission['group_name'];
            
            if (!isset($groupedPermissions[$groupName])) {
                $groupedPermissions[$groupName] = [];
            }
            
            $groupedPermissions[$groupName][] = $permission;
        }
        
        $this->view('roles/permissions', [
            'title' => 'Управление правами для роли: ' . ucfirst($role),
            'role' => $role,
            'groupedPermissions' => $groupedPermissions
        ]);
    }
} 