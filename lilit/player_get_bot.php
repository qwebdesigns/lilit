<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
// Подключение к базе данных
$host = 'localhost';
$db = 'lilit';
$user = 'root';
$pass = 'R6O_Qdg9scd3aKgL';

$mysqli = new mysqli($host, $user, $pass, $db);

if ($mysqli->connect_error) {
    die("Ошибка подключения: " . $mysqli->connect_error);
}

if (isset($_GET['link'])) {
    // Экранируем и получаем значение link
    $link = $mysqli->real_escape_string($_GET['link']);

    // Ищем запись в базе данных
    $sql = "SELECT * FROM players WHERE link LIKE '%$link%'";
    $result = $mysqli->query($sql);

    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();

        $accounts = str_replace('^', '<br>[', $row['accounts']);
        $accounts_formated = str_replace('|', '] ', $accounts);
        
        // Выводим все поля профиля
        echo "ID: " . $row['id'] . "<br>";
        echo "Ссылка: " . $row['link'] . "<br>";
        echo "Аккаунты: [" . $accounts_formated . "<br>";
        echo "Любимое оружие: " . $row['favorit'] . "<br>";
        echo "Соцсети: " . $row['socials'] . "<br>";
        echo "Описание: " . $row['description'] . "<br>";
        echo "Любимые карты: " . $row['maps'] . "<br>";
        echo "Средний K/D: " . $row['kd'] . "<br>";
        echo "Награды: " . $row['awards'] . "<br>";
        echo "Титул: " . $row['titul'] . "<br>";

    } else {
        echo "Профиль не найден(";
    }
} else {
    echo "Не указан параметр link.";
}

$mysqli->close();
?>