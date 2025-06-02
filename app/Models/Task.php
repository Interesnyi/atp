<?php
namespace App\Models;

use App\Core\Model;

class Task extends Model {
    protected $table = 'tasks';

    public function getAllTasks() {
        $sql = "SELECT t.*, u.username as executor_name FROM {$this->table} t LEFT JOIN users u ON t.executor_id = u.id ORDER BY t.due_date ASC";
        return $this->db->fetchAll($sql);
    }

    public function getTaskById($id) {
        $sql = "SELECT * FROM {$this->table} WHERE id = ?";
        return $this->db->fetch($sql, [$id]);
    }

    public function createTask($data) {
        $sql = "INSERT INTO {$this->table} (title, description, executor_id, due_date, remind_at, status, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, NOW(), NOW())";
        $this->db->execute($sql, [
            $data['title'],
            $data['description'],
            $data['executor_id'],
            $data['due_date'],
            $data['remind_at'],
            $data['status'] ?? 'active'
        ]);
        return $this->db->lastInsertId();
    }

    public function updateTask($id, $data) {
        $sql = "UPDATE {$this->table} SET title=?, description=?, executor_id=?, due_date=?, remind_at=?, status=?, updated_at=NOW() WHERE id=?";
        return $this->db->execute($sql, [
            $data['title'],
            $data['description'],
            $data['executor_id'],
            $data['due_date'],
            $data['remind_at'],
            $data['status'],
            $id
        ]) > 0;
    }

    public function deleteTask($id) {
        $sql = "DELETE FROM {$this->table} WHERE id = ?";
        return $this->db->execute($sql, [$id]) > 0;
    }

    public function getTasksToRemind($now) {
        $sql = "SELECT * FROM {$this->table} WHERE status = 'active' AND remind_at IS NOT NULL AND remind_at <= ? AND notified_at IS NULL";
        return $this->db->fetchAll($sql, [$now]);
    }

    public function markTaskNotified($id) {
        $sql = "UPDATE {$this->table} SET notified_at = NOW() WHERE id = ?";
        return $this->db->execute($sql, [$id]);
    }

    public function getNotesByTaskId($taskId) {
        $sql = "SELECT * FROM tasks_notes WHERE task_id = ? ORDER BY created_at ASC";
        return $this->db->fetchAll($sql, [$taskId]);
    }

    public function addNote($taskId, $note) {
        $sql = "INSERT INTO tasks_notes (task_id, note, created_at) VALUES (?, ?, NOW())";
        $this->db->execute($sql, [$taskId, $note]);
        return $this->db->lastInsertId();
    }

    public function getFilesByNoteId($noteId) {
        $sql = "SELECT * FROM tasks_notes_files WHERE note_id = ? ORDER BY uploaded_at ASC";
        return $this->db->fetchAll($sql, [$noteId]);
    }

    public function addNoteFile($noteId, $fileName, $filePath) {
        $sql = "INSERT INTO tasks_notes_files (note_id, file_name, file_path, uploaded_at) VALUES (?, ?, ?, NOW())";
        $this->db->execute($sql, [$noteId, $fileName, $filePath]);
        return $this->db->lastInsertId();
    }
} 