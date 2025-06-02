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
            if (empty($data['remind_at'])) {
                $data['remind_at'] = null;
            }
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

    public function show($id) {
        $taskModel = new Task();
        $userModel = new User();
        $task = $taskModel->getTaskById($id);
        $users = $userModel->getAllUsers();
        $notes = $taskModel->getNotesByTaskId($id);
        $this->view->render('tasks/show', [
            'task' => $task,
            'users' => $users,
            'notes' => $notes,
            'title' => 'Дело: ' . htmlspecialchars($task['title'])
        ]);
    }

    public function addNote($id) {
        $taskModel = new Task();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $note = trim($_POST['note'] ?? '');
            $errors = [];
            if ($note !== '') {
                $noteId = $taskModel->addNote($id, $note);
                // Обработка файлов
                if (!empty($_FILES['note_files']) && is_array($_FILES['note_files']['name'])) {
                    $uploadDir = __DIR__ . '/../../public/uploads/tasks_notes/';
                    if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
                    foreach ($_FILES['note_files']['name'] as $i => $name) {
                        if ($_FILES['note_files']['error'][$i] === UPLOAD_ERR_OK) {
                            $tmpName = $_FILES['note_files']['tmp_name'][$i];
                            $safeName = uniqid().'-'.basename($name);
                            $targetPath = $uploadDir . $safeName;
                            if (move_uploaded_file($tmpName, $targetPath)) {
                                $taskModel->addNoteFile($noteId, $name, '/uploads/tasks_notes/' . $safeName);
                            } else {
                                $errors[] = "Ошибка загрузки файла: $name";
                            }
                        } elseif ($_FILES['note_files']['error'][$i] !== UPLOAD_ERR_NO_FILE) {
                            $errors[] = "Ошибка загрузки файла: $name";
                        }
                    }
                }
                $_SESSION['success'] = 'Заметка добавлена';
                if ($errors) $_SESSION['error'] = implode('<br>', $errors);
            }
            $this->redirect('/tasks/show/' . $id);
        }
    }
} 