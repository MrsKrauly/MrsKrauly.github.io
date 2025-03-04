<?php
session_start(); // Включение сессий
error_reporting(E_ALL);
ini_set('display_errors', 1);

$servername = "localhost"; // ваш сервер БД
$username = "root"; // ваш пользователь БД
$password = "root"; // ваш пароль БД
$dbname = "my_website"; // имя базы данных

// подключение
$conn = new mysqli($servername, $username, $password, $dbname);

// Проверка подключения
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Получение данных о пользователе по ID из сессии
if (isset($_SESSION['user_id'])) {
    $userId = $_SESSION['user_id'];
    $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if (!$user) {
        die("Пользователь не найден.");
    }
} else {
    die("Пожалуйста, войдите в систему.");
}

// Продолжайте с кодом для отображения личного кабинета...


// Обработка формы
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $doctor = $_POST['doctor'];
    
    // Обновление данных о пользователе в базе данных
    $sql = "UPDATE users SET name = ?, email = ?, phone = ?, doctor = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssi", $name, $email, $phone, $doctor, $userId);
    $stmt->execute();

    // Обработка загрузки изображения
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
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Личный кабинет</title>
    <link rel="stylesheet" href="dash_styles.css">
    <style>
        /* Скрываем форму по умолчанию */
        .edit-form {
            display: none;
            opacity: 0; /* Начальное значение для анимации */
            transition: opacity 0.5s ease; /* Плавная анимация */
        }
        .edit-form.show {
            display: block; /* Показываем форму */
            opacity: 1; /* Полная непрозрачность */
        }
    </style>
</head>
<body>
    <header>
        <h1>Личный кабинет</h1>
        <nav>
            <ul>
                <li><a href="#profile">Профиль</a></li>
                <li><a href="#appointments">История записей</a></li>
                <li><a href="#settings">Настройки</a></li>
                <li><a href="logout.php" class="btn">Выйти</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <section id="profile" class="profile">
            <h2>Информация о пользователе</h2>
            <div class="profile-info">
                <img src="<?= !empty($user['photo']) ? $user['photo'] : 'default.jpg' ?>" alt="Фотография пациента" class="profile-photo" id="current-photo">
                
                <div class="user-details">
                    <p><strong>Имя:</strong> <?= $user['name'] ?? 'Не указано' ?></p>
                    <p><strong>E-mail:</strong> <?= $user['email'] ?? 'Не указано' ?></p>
                    <p><strong>Телефон:</strong> <?= $user['phone'] ?? 'Не указано' ?></p>
                    <p><strong>Лечащий врач:</strong> <?= $user['doctor'] ?? 'Не указано' ?></p>
                    <button class="btn edit-btn" id="edit-button">Изменить данные</button>
                </div>
            </div>
        
            <section class="upload-section">
                <h2 class="small-title">Загрузите вашу фотографию</h2>
                <input type="file" id="file-input" name="photo" accept="image/*" class="file-input">
                <div class="image-preview">
                    <img id="preview" src="" alt="Предварительный просмотр" style="display: none; max-width: 100%; border-radius: 10px;">
                </div>
            </section>

            <!-- Форма для заполнения данных о пользователе -->
            <h2 class="small-title">Заполните информацию о себе</h2>
            <form class="edit-form" method="POST" action="" enctype="multipart/form-data">
                <label for="name">Имя:</label>
                <input type="text" id="name" name="name" required>
                
                <label for="email">E-mail:</label>
                <input type="email" id="email" name="email" required>
                
                <label for="phone">Телефон:</label>
                <input type="text" id="phone" name="phone" required>
                
                <label for="doctor">Лечащий врач:</label>
                <input type="text" id="doctor" name="doctor" required>
                
                <label for="photo">Загрузите вашу фотографию:</label>
                <input type="file" id="photo" name="photo" accept="image/*">
                
                <button type="submit" class="btn">Сохранить данные</button>
            </form>
        </section>

        <section id="appointments" class="appointments">
            <h2>История записей</h2>
            <ul>
                <li>Прием у диетолога - 25.10.2023</li>
                <li>Прием у диетолога - 12.11.2023</li>
            </ul>
        </section>

        <section id="settings" class="settings">
            <h2>Настройки</h2>
            <form>
                <label for="new-password">Новый пароль:</label>
                <input type="password" id="new-password" placeholder="Введите новый пароль">
                <button type="submit" class="btn">Сохранить</button>
            </form>
        </section>
    </main>

    <footer class="contact">
        <h2>Контакты</h2>
        <p>Телефон: +9373297917</p>
        <p>Имейл: 1@gmail.com</p>
    </footer>
    
    <script>
        const fileInput = document.getElementById('file-input');
        const currentPhoto = document.getElementById('current-photo');
        const editButton = document.getElementById('edit-button');
        const editForm = document.querySelector('.edit-form');

        fileInput.addEventListener('change', function(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    currentPhoto.src = e.target.result; // Заменяем источник текущего изображения
                }
                reader.readAsDataURL(file);
            }
        });

        // Показать/скрыть форму с анимацией
        editButton.addEventListener('click', function() {
            if (editForm.classList.contains('show')) {
                editForm.classList.remove('show');
                setTimeout(() => {
                    editForm.style.display = 'none';
                }, 500); // Время задержки до скрытия элемента
            } else {
                editForm.style.display = 'block';
                setTimeout(() => {
                    editForm.classList.add('show');
                }, 0); // Немедленно добавляем класс для анимации
            }
        });
    </script>
</body>
</html>
