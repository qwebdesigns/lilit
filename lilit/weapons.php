<?php
// Подключение к базе данных
$host = 'localhost'; // Замените на ваш хост
$db = 'lilit'; // Замените на имя вашей базы данных
$user = 'root'; // Замените на ваше имя пользователя
$pass = 'R6O_Qdg9scd3aKgL'; // Замените на ваш пароль (если он есть)

// Создание подключения
$mysqli = new mysqli($host, $user, $pass, $db);

// Проверка подключения
if ($mysqli->connect_error) {
    die("Ошибка подключения: " . $mysqli->connect_error);
}

// Получение параметра alias из URL
$alias = isset($_GET['alias']) ? $mysqli->real_escape_string($_GET['alias']) : '';

// Проверка, был ли передан параметр alias
if ($alias) {
    // SQL-запрос для поиска по alias
    echo 'О, нашлось) Воть информация по твоему запросу: ' . $alias . "<br>";
    echo " <br>";
    $sql = "SELECT * FROM items WHERE item_name_ru = '$alias'";
    $result = $mysqli->query($sql);


    /*
    Название: SSG-69
        Тип: Оружие
        Категория: Снайперские винтовки
        Минимальный уровень: 5
        Видимость: Предмет по уровню
        Тема: Стандарт
        Стоимость в золоте: 46
        Улучшения:
        - Урон: от 115 до 150 (золото: 0 - 20)
        - Обойма: от 5 до 10 (золото: 0 - 5)
        - Запас: от 15 до 40 (золото: 0 - 5)
        - Темп: 24 (без улучшений)
        - Дальность: 140 (без улучшений)
        - Перезарядка: 44 (без улучшений)

        Иконка:







                1<th>alias</th>
                        2<th>icon</th>
                3<th>type_id</th>
                        4<th>type_name</th>
                5<th>category_id</th>
                        6<th>category_name</th>
                        7<th>item_min_lvl</th>
                8<th>item_visible_id</th>
                        9<th>item_visible_name</th>
                10<th>item_theme_id</th>
                        11<th>item_theme_name</th>
                        12<th>item_cost_gold</th>
                13<th>item_buy_amount</th>
                        14<th>item_name_ru</th>
                        15<th>item_name_en</th>
                        16<th>upgrades</th>
    */
    
    // Проверка на наличие результатов
    if ($result->num_rows > 0) {
        // Вывод данных каждой строки
        while ($row = $result->fetch_assoc()) {
            echo "Название: " . htmlspecialchars($row['item_name_ru']) . " \ " . htmlspecialchars($row['item_name_en']) . '<br>';
            echo "Категория: " . htmlspecialchars(mb_convert_case($row['category_name'], MB_CASE_TITLE, "UTF-8")) . '<br>';
            echo "Тип: " . htmlspecialchars($row['type_name']) . '<br>';
            echo "Минимальный уровень: " . htmlspecialchars($row['item_min_lvl']) . '<br>';
            echo "Стоимость в золоте: " . htmlspecialchars($row['item_cost_gold']) . '<br>';
            echo "Тематика: " . htmlspecialchars($row['item_theme_name']) . '<br>';
            echo "Доступность: " . htmlspecialchars($row['item_visible_name']) . '<br>';

            // Декодируем JSON-строку в массив
            $data = json_decode($row['upgrades'], true);

            // Формируем шаблон
            $template = "Улучшения:<br>";
            foreach ($data as $item) {
                $template .= $item['name'] . ":<br>";
                foreach ($item['levels'] as $level) {
                    $template .= "Уровень " . ($level['id'] + 1) . " = " . $level['value'] . ", цена " . $level['gold'] . "<br>";
                }
            }

            // Выводим результат
            echo $template;
            echo "Иконка: " . htmlspecialchars($row['icon']) . '<br>';
        }
    } else {
        echo "Нет результатов для поиска по запросу: " . htmlspecialchars($alias);
    }
} else {
    echo "Пожалуйста, укажите параметр alias в URL.";
}

// Закрытие подключения к базе данных
$mysqli->close();
?>