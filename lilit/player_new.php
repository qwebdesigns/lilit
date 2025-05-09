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
    $id = $mysqli->real_escape_string($_GET['main-game-id']);
    $link = $mysqli->real_escape_string($_GET['vk-id']);
    $favorit = $mysqli->real_escape_string($_GET['fav-weapon']);
    $socials = $mysqli->real_escape_string($_GET['socials']);
    $description = $mysqli->real_escape_string($_GET['description']);
    $maps = $mysqli->real_escape_string($_GET['fav-map']);
    $kd = $mysqli->real_escape_string($_GET['avg-kd']);
    $awards = $mysqli->real_escape_string($_GET['awards']);
    $titul = $mysqli->real_escape_string($_GET['title']);

    // Проверка существования записи
    $check_sql = "SELECT id FROM players WHERE id = '$id'";
    $result = $mysqli->query($check_sql);

    if ($result && $result->num_rows > 0) {
        echo "Аккаунт уже существует";
    } else {
        // Форматирование accounts
        if (is_array($_GET['level']) && is_array($_GET['nickname'])) {
            $accountsArray = [];
            foreach ($_GET['level'] as $index => $level) {
                $nickname = $mysqli->real_escape_string($_GET['nickname'][$index]);
                $accountsArray[] = $mysqli->real_escape_string($level) . '|' . $nickname;
            }
            $accounts = implode('^', $accountsArray);
        } else {
            $level = $mysqli->real_escape_string($_GET['level']);
            $nickname = $mysqli->real_escape_string($_GET['nickname']);
            $accounts = $level . '|' . $nickname;
        }

        // Подготовка SQL-запроса для вставки
        $sql = "INSERT INTO players (id, link, accounts, favorit, socials, description, maps, kd, awards, titul) 
                VALUES ('$id', '$link', '$accounts', '$favorit', '$socials', '$description', '$maps', '$kd', '$awards', '$titul')";

        if ($mysqli->query($sql) === TRUE) {
            echo "Запись успешно добавлена";
        } else {
            echo "Ошибка: " . $mysqli->error;
        }
    }
} else {
    echo "<h2>Нет данных в GET-запросе</h2>";
}

$mysqli->close();
?>