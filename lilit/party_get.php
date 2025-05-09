<?php
header('Content-Type: text/plain; charset=UTF-8');

// Подключение к базе данных
$host = 'localhost';
$db = 'lilit';
$user = 'root';
$pass = 'R6O_Qdg9scd3aKgL';

$mysqli = new mysqli($host, $user, $pass, $db);

if ($mysqli->connect_error) {
    die('Ошибка подключения: ' . $mysqli->connect_error);
}

// Получаем все мероприятия из базы данных
$sql = "SELECT * FROM party";
$result = $mysqli->query($sql);

$currentTime = time();
$futureEvents = [];

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $eventTimestamp = strtotime($row['data']);

        if ($eventTimestamp && $eventTimestamp > $currentTime) {
            $futureEvents[] = [
                'date' => $row['data'],
                'rules' => explode(', ', $row['rules']),
                'type' => $row['type'],
                'map' => $row['map'],
                'skins' => $row['skins']
            ];
        }
    }
}

// Выводим результат
if (!empty($futureEvents)) {
    foreach ($futureEvents as $event) {
        // Форматируем дату: заменяем T на " в " и преобразуем формат
        $formattedDate = date('d.m.Y в H:i', strtotime($event['date']));

        echo "@online В ближайшее время запланированно!..\n";
        echo " \n";
        echo "⌛ Дата: " . $formattedDate . "\n";
        echo "🚩 Тип мероприятия: " . $event['type'] . "\n";
        echo "🗺️ Игровая карта: " . $event['map'] . "\n";
        echo "🎭 Доступные скины: " . $event['skins'] . "\n";
        echo "🚫 Правила: \n";
        foreach ($event['rules'] as $rule) {
            echo "- Запрещенно: " . trim($rule) . "\n";
        }
        echo "=================\n";
    }
} else {
    echo "Предстоящих мероприятий не найдено.";
}

$mysqli->close();
?>