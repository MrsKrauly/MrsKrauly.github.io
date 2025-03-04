<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$servername = "localhost"; // или ваш сервер БД
$username = "root"; // ваш пользователь БД
$password = "root"; // ваш пароль БД
$dbname = "my_website"; // имя базы данных

// Создаем соединение
$conn = new mysqli($servername, $username, $password, $dbname);

// Проверяем соединение
if ($conn->connect_error) {
    die("Ошибка подключения: " . $conn->connect_error);
}

// Получаем данные из формы
$email = $_POST['email'];
$name = $_POST['name'];
$password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Хэшируем пароль
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);
// Проверка на существование email
$email_check_sql = "SELECT id FROM users WHERE email = '$email'";
$result = $conn->query($email_check_sql);

if ($result->num_rows > 0) {
    echo "Ошибка: Пользователь с таким адресом электронной почты уже существует.";
} else {
    // Подготовка и выполнение SQL запроса для вставки нового пользователя
    $sql = "INSERT INTO users (email, name, password) VALUES ('$email', '$name', '$password')";

    if ($conn->query($sql) === TRUE) {
        echo "Регистрация успешна!";
    } else {
        echo "Ошибка: " . $conn->error;
    }
}

// Закрываем соединение
$conn->close();
?>
