document.getElementById('diet-form').addEventListener('submit', function(event) {
    event.preventDefault();

    const age = parseInt(this[0].value);
    const weight = parseFloat(this[1].value); // Получаем вес из формы
    const height = parseFloat(this[2].value) / 100; // Получаем рост из формы и переводим в метры
    const activityLevel = parseInt(this[3].value);

    // Расчет индекса массы тела (ИМТ)
    const bmi = weight / (height * height); // Расчет ИМТ
    let bmiCategory;

    // Определение категории ИМТ
    if (bmi < 18.5) {
        bmiCategory = 'Недостаточная масса тела';
    } else if (bmi >= 18.5 && bmi < 24.9) {
        bmiCategory = 'Нормальная масса тела';
    } else if (bmi >= 25 && bmi < 29.9) {
        bmiCategory = 'Избыточная масса тела';
    } else {
        bmiCategory = 'Ожирение';
    }

    const bmr = 10 * weight + 6.25 * height * 100 - 5 * age + 5; // Формула Миффлина - Сент Жеора
    let calorieNeeds;

    // Определяем потребности в калориях на основе уровня физической активности
    switch (activityLevel) {
        case 1: // Малоактивный
            calorieNeeds = Math.floor(bmr * 1.2);
            break;
        case 2: // Умеренные нагрузки
            calorieNeeds = Math.floor(bmr * 1.55);
            break;
        case 3: // Активные нагрузки
            calorieNeeds = Math.floor(bmr * 1.9);
            break;
    }

    // Обновляем результаты на странице
    document.getElementById('diet-recommendation').innerText = 
        `Индекс массы тела (ИМТ): ${bmi.toFixed(2)} (${bmiCategory}). Вам рекомендуется примерно ${calorieNeeds} калорий в день, чтобы поддерживать текущий вес.`;
    document.getElementById('result').style.display = 'block';
});