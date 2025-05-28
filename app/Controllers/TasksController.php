<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\Task;
use App\Models\User;

class TasksController extends Controller {
    public function index() {
        $taskModel = new Task();
        $tasks = $taskModel->getAllTasks();
        $this->view->render('tasks/index', [
            'tasks' => $tasks,
            'title' => 'Дела'
        ]);
    }

    public function create() {
        $userModel = new User();
        $users = $userModel->getAllUsers();
        $this->view->render('tasks/create', [
            'users' => $users,
            'title' => 'Добавить дело'
        ]);
    }

    public function store() {
        $taskModel = new Task();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = $_POST;
            $taskModel->createTask($data);
            $_SESSION['success'] = 'Дело успешно добавлено';
            $this->redirect('/tasks');
        }
    }

    public function edit($id) {
        $taskModel = new Task();
        $userModel = new User();
        $task = $taskModel->getTaskById($id);
        $users = $userModel->getAllUsers();
        $this->view->render('tasks/edit', [
            'task' => $task,
            'users' => $users,
            'title' => 'Редактировать дело'
        ]);
    }

    public function update($id) {
        $taskModel = new Task();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = $_POST;
            $taskModel->updateTask($id, $data);
            $_SESSION['success'] = 'Дело успешно обновлено';
            $this->redirect('/tasks');
        }
    }

    public function delete($id) {
        $taskModel = new Task();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $taskModel->deleteTask($id);
            $_SESSION['success'] = 'Дело удалено';
            $this->redirect('/tasks');
        }
    }

    public function notify() {
        $log = require __DIR__ . '/../../notify_tasks.php';
        $_SESSION['success'] = 'Уведомления отправлены!';
        $_SESSION['notify_log'] = $log;
        $this->redirect('/tasks');
    }
} 