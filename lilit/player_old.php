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

    // Проверка существования записи
    $check_sql = "SELECT id FROM players WHERE id = '$id'";
    $result = $mysqli->query($check_sql);

    if ($result && $result->num_rows > 0) {
        // Если запись существует, выполняем UPDATE
        $update_sql = "UPDATE players SET 
            link='$link', 
            accounts='$accounts', 
            favorit='$favorit', 
            socials='$socials', 
            description='$description', 
            maps='$maps', 
            kd='$kd', 
            awards='$awards', 
            titul='$titul' 
            WHERE id='$id'";

        if ($mysqli->query($update_sql) === TRUE) {
            echo "Запись успешно обновлена.";
        } else {
            echo "Ошибка при обновлении: " . $mysqli->error;
        }
    } else {
        echo "Запись с таким ID не найдена.";
    }
} else {
    echo "<h2>Нет данных в GET-запросе</h2>";
}

$mysqli->close();
?>