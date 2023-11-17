<?php

require("./Database.php");
require("./response.php");

function sendAllTasks() {
    $sql = "SELECT * FROM tasks";
    Database::query($sql);
    $rows = Database::getAll();

    // Set content type to JSON
    header('Content-Type: application/json');

    // Send a JSON response with indentation
    echo json_encode(['tasks' => $rows], JSON_PRETTY_PRINT);
}

function sendOneTask() {
    if (!isset($_GET['id'])) {
        http_response_code(404);
        die();
    }

    $id = $_GET['id'];

    $sql = "SELECT * FROM tasks WHERE id = :id";
    $placeholders = [':id' => $id];
    Database::query($sql, $placeholders);
    $row = Database::get();

    if ($row === false) {
        // Handle the database error, maybe log it
        http_response_code(500);
        die();
    }

    // Set content type to JSON
    header('Content-Type: application/json');

    // Send a JSON response with indentation
    echo json_encode(['task' => $row], JSON_PRETTY_PRINT);
}

function addTask() {
    // Check if the required parameters are present
    $data = json_decode(file_get_contents("php://input"), true);

    if (!isset($data['content'])) {
        http_response_code(400);
        die();
    }

    $content = $data['content'];

    // Insert the new task into the database
    $sql = "INSERT INTO tasks (content, completed) VALUES (:content, 0)";
    $placeholders = [':content' => $content];
    Database::query($sql, $placeholders);

    // Optionally, you can get the ID of the newly inserted task
    $newTaskId = Database::getLastInsertId();

    // Return a success message or the ID of the newly inserted task
    response(['message' => 'Task added successfully', 'new_task_id' => $newTaskId]);
}

function deleteTask() {
    // Check if the required parameters are present
    if (!isset($_GET['id'])) {
        http_response_code(400);
        die();
    }

    $id = $_GET['id'];

    // Delete the task from the database
    $sql = "DELETE FROM tasks WHERE id = :id";
    $placeholders = [':id' => $id];
    Database::query($sql, $placeholders);

    // Return a success message
    response(['message' => 'Task deleted successfully']);
}

function updateCompletionStatus() {
    if (!isset($_GET['id']) || !isset($_GET['completed'])) {
        http_response_code(400);
        die();
    }

    $id = $_GET['id'];
    $completed = $_GET['completed'];

    $sql = "UPDATE tasks SET completed = :completed WHERE id = :id";
    $placeholders = [':completed' => $completed, ':id' => $id];

    Database::query($sql, $placeholders);

    response(['message' => 'Task completion status updated']);
}