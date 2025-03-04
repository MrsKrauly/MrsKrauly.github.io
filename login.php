<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Параметры подключения к базе данных
$servername = "localhost"; 
$username = "root"; 
$password = "root"; 
$dbname = "my_website"; 

// Создаем подключение
$conn = new mysqli($servername, $username, $password, $dbname);

// Проверка подключения
if ($conn->connect_error) {
    die("Ошибка подключения: " . $conn->connect_error);
}

// Обработка POST-запроса
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Получаем данные из POST-запроса и проверяем их
    $email = isset($_POST['email']) ? $_POST['email'] : null;
    $password = isset($_POST['password']) ? $_POST['password'] : null;

    if ($email && $password) {
        // Подготавливаем SQL-запрос
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->bind_param("s", $email); // "s" означает строку
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            // Проверяем пароль
            if (password_verify($password, $row['password'])) {
                // Успешный вход, сохраните ID пользователя в сессии
                $_SESSION['user_id'] = $row['id'];
                echo "Успешный вход!";
            } else {
                echo "Неверные учетные данные.";
            }
        } else {
            echo "Неверные учетные данные.";
        }

        // Закрываем подготовленный запрос
        $stmt->close();
    } else {
        echo "Пожалуйста, заполните все поля.";
    }
} else {
    echo "Некорректный запрос. Метод запроса: " . $_SERVER["REQUEST_METHOD"];
}

// Закрываем соединение с базой данных
$conn->close();
?>

