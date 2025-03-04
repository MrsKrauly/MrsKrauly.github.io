<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Личный кабинет</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="mainpage_styles.css">
</head>
<body>
    <div class="container">
        <aside class="sidebar">
            <h2>Меню</h2>
            <a href="dashboard.php" class="sidebar-btn">Личный кабинет</a>
            <a href="#diet" class="sidebar-btn">Диета</a>
            <a href="#appointments" class="sidebar-btn">Записи</a>
            <a href="diet-calculator.html" class="sidebar-btn">Рассчитать диету с помощью ИИ (beta)</a> <!-- Новая кнопка -->

        </aside>

        <main class="main-content">
            <h1>Добро пожаловать в ваш личный кабинет!</h1>
            <div class="info-block">
                <h2>Полезная информация</h2>
                <p>Здесь будет информация о ваших диетах, рекомендациях и зонах для улучшения.</p>
            </div>
            <div class="info-block">
                <h2>Ваши записи</h2>
                <p>Посмотрите ваши будущие и прошлые записи к врачу.</p>
            </div>
            <div class="info-block">
                <h2>Рекомендации</h2>
                <p>Проверьте последние рекомендации по питанию и образу жизни.</p>
            </div>

            <!-- Новый раздел "Как вы сегодня себя чувствуете?" -->
            <div class="info-block">
                <h2>Как вы сегодня себя чувствуете?</h2>
                <form id="mood-form" class="mood-form">
                    <label for="mood">Настроение:</label>
                    <select id="mood" required>
                        <option value="">Выберите настроение...</option>
                        <option value="happy">Счастлив</option>
                        <option value="neutral">Нейтрально</option>
                        <option value="sad">Грустен</option>
                    </select>
                    
                    <label for="fatigue">Ощущение усталости:</label>
                    <select id="fatigue" required>
                        <option value="">Выберите уровень усталости...</option>
                        <option value="low">Низкий</option>
                        <option value="medium">Средний</option>
                        <option value="high">Высокий</option>
                    </select>
                    
                    <label for="weight">Вес:</label>
                    <input type="number" id="weight" placeholder="Ваш вес (кг)" required>
                    
                    <button type="submit" class="btn">Отправить</button>
                </form>
                <div id="chart-container">
                    <canvas id="moodChart"></canvas>
                </div>
            </div>

            <!-- Кнопка "Рассчитать свой план диеты" -->
            <div class="info-block">
                <h2>Ваш диетический план</h2>
                <a href="diet.html" class="btn">Рассчитать свой план диеты</a>
            </div>
        </main>

        <div class="chat-button">
            <a href="#chat" class="btn">Чат с врачом</a>
        </div>
    </div>

    <footer class="contact">
        <div class="contact-info">
            <h2>Контакты</h2>
            <p>Телефон: +9373297917</p>
            <p>Email: 1@gmail.com</p>
        </div>
    </footer>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const moodForm = document.getElementById("mood-form");
            const moodInput = document.getElementById("mood");
            const fatigueInput = document.getElementById("fatigue");
            const weightInput = document.getElementById("weight");
            const chartContainer = document.getElementById("chart-container");
            const moodChartCanvas = document.getElementById("moodChart");
        
            // Проверяем, заполнена ли форма сегодня
            const today = new Date().toDateString();
            const savedData = JSON.parse(localStorage.getItem("moodData")) || {};
            
            if (savedData.date === today) {
                // Если данные уже сохранены на сегодня, отображаем график
                displayChart(savedData);
                moodForm.style.display = "none"; // Скрываем форму
            } else {
                moodForm.addEventListener("submit", function(event) {
                    event.preventDefault(); // Предотвращаем стандартное поведение формы
                    
                    const mood = moodInput.value;
                    const fatigue = fatigueInput.value;
                    const weight = weightInput.value;
        
                    // Сохраняем данные
                    const moodData = {
                        date: today,
                        mood: mood,
                        fatigue: fatigue,
                        weight: weight
                    };
                    localStorage.setItem("moodData", JSON.stringify(moodData));
        
                    // Отображаем график
                    displayChart(moodData);
                    moodForm.style.display = "none"; // Скрываем форму
                });
            }
        
            function displayChart(data) {
                const ctx = moodChartCanvas.getContext('2d');
                const moodValue = getMoodValue(data.mood);
                const fatigueValue = getFatigueValue(data.fatigue);
        
                const chart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: ['Настроение', 'Усталость', 'Вес'],
                        datasets: [{
                            label: 'Значения',
                            data: [moodValue, fatigueValue, data.weight],
                            backgroundColor: ['#4CAF50', '#FF9800', '#2196F3'],
                        }]
                    },
                    options: {
                        responsive: true,
                        scales: {
                            y: {
                                beginAtZero: true,
                                max: 10 // Устанавливаем максимальное значение для визуализации
                            }
                        }
                    }
                });
            }
        
            function getMoodValue(mood) {
                switch (mood) {
                    case "happy": return 10;
                    case "neutral": return 5;
                    case "sad": return 1;
                    default: return 0;
                }
            }
        
            function getFatigueValue(fatigue) {
                switch (fatigue) {
                    case "low": return 3;
                    case "medium": return 6;
                    case "high": return 9;
                    default: return 0;
                }
            }
        });
        </script>
</body>
</html>
