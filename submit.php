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
$phone = $_POST['phone'];

// Подготовка и выполнение SQL запроса
$sql = "INSERT INTO appointments (email, username, phone) VALUES ('$email', '$name', '$phone')";

if ($conn->query($sql) === TRUE) {
    echo "Запись успешно добавлена!";
} else {
    echo "Ошибка: " . $conn->error;
}

// Закрываем соединение
$conn->close();
?>
