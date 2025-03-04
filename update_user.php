<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$servername = "localhost"; // или ваш сервер БД
$username = "root"; // ваш пользователь БД
$password = "root"; // ваш пароль БД
$dbname = "my_website"; // имя базы данных

// подключение
$conn = new mysqli($servername, $username, $password, $dbname);

// Проверка подключения
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Обработка запроса
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId = intval($_POST['user_id']);
    $name = $_POST['name'];
    $phone = $_POST['phone'];
    $doctor = $_POST['doctor'];

    // Обновление данных о пользователе в базе данных (не трогаем email и password)
    $sql = "UPDATE users SET name = ?, phone = ?, doctor = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssi", $name, $phone, $doctor, $userId);

    $response = [];

    if ($stmt->execute()) {
        $response['status'] = 'success';

        // Обработка загрузки нового изображения
        if (isset($_FILES['photo']) && $_FILES['photo']['error'] == UPLOAD_ERR_OK) {
            $targetDir = "uploads/"; // Директория для загрузки
            $targetFile = $targetDir . basename($_FILES["photo"]["name"]);
            
            if (move_uploaded_file($_FILES["photo"]["tmp_name"], $targetFile)) {
                // Обновите поле photo в базе данных
                $sql = "UPDATE users SET photo = ? WHERE id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("si", $targetFile, $userId);
                $stmt->execute();
            }
        }
    } else {
        $response['status'] = 'error';
        $response['message'] = 'Не удалось обновить данные.';
    }

    // Возвращаем ответ в виде JSON
    echo json_encode($response);
}

$conn->close();
?>