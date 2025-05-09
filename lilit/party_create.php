<?php
// Подключение к базе данных
$host = 'localhost';
$db = 'lilit';
$user = 'root';
$pass = 'R6O_Qdg9scd3aKgL';

$mysqli = new mysqli($host, $user, $pass, $db);

if ($mysqli->connect_error) {
    die("Ошибка подключения: " . $mysqli->connect_error);
}

if (!empty($_GET)) {
    // Экранирование специальных символов
    $datetime = $mysqli->real_escape_string($_GET['datetime']);
    $type = $mysqli->real_escape_string($_GET['type']);
    $map = $mysqli->real_escape_string($_GET['map']);
    $skins = $mysqli->real_escape_string($_GET['skins']);

    // Обработка правил (может быть массивом)
    $rules = '';
    if (isset($_GET['rules'])) {
        if (is_array($_GET['rules'])) {
            $rulesArray = array_map(function ($item) use ($mysqli) {
                return $mysqli->real_escape_string($item);
            }, $_GET['rules']);
            $rules = implode(', ', $rulesArray);
        } else {
            $rules = $mysqli->real_escape_string($_GET['rules']);
        }
    }

    // SQL-запрос для вставки
    $sql = "INSERT INTO party (data, rules, type, map, skins) 
            VALUES ('$datetime', '$rules', '$type', '$map', '$skins')";

    if ($mysqli->query($sql) === TRUE) {
        $response = "Успешно добавлено!<br>";
        $response .= "Дата: $datetime<br>";
        $response .= "Правила: $rules<br>";
        $response .= "Тип: $type<br>";
        $response .= "Карта: $map<br>";
        $response .= "Скины: $skins";
        echo $response;
    } else {
        echo "Ошибка добавления: " . $mysqli->error;
    }
} else {
    echo "<h2>Нет данных в GET-запросе</h2>";
}

$mysqli->close();
?>