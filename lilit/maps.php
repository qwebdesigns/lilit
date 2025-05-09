<?php
function handle_response($response_text, $pdo)
{
    // Разделяем текст по символу '^' и фильтруем пустые строки
    $lines = array_filter(array_map('trim', explode('^', $response_text)), 'strlen');

    // Преобразуем строки в многомерный массив и удаляем указанные элементы
    $multi_dimensional_array = [];
    foreach ($lines as $line) {
        $parts = explode('|', $line);
        $filtered = [];
        foreach ($parts as $index => $part) {
            if (!in_array($index, [2, 7, 8, 9, 10])) {
                $filtered[] = $part;
            }
        }
        $multi_dimensional_array[] = $filtered;
    }

    // Создаем ассоциативные массивы
    $keys = ["st1", "Режим", "Номер сервера", "кол-игроков", "Макс. игроков", "Айди или ник"];
    $json_output = [];
    foreach ($multi_dimensional_array as $arr) {
        $obj = [];
        foreach ($keys as $i => $key) {
            if (isset($arr[$i])) {
                $obj[$key] = $arr[$i];
            }
        }
        $json_output[] = $obj;
    }

    // Фильтруем по количеству игроков
    $filtered_output = array_filter($json_output, function ($obj) {
        return isset($obj['кол-игроков']) && $obj['кол-игроков'] !== '0';
    });

    // Заменяем значения режимов
    $mode_mapping = [
        '2' => "⛏️Стройка",
        '5' => "😍Контра",
        '0' => "🚯Битва",
        '3' => "📱Зомби",
        '14' => "🚩Гангейм",
        '6' => "🗡️Резня",
        '7' => "⌛Выживание",
        '12' => "❄️Снежки",
        '8' => "1⃣9⃣4⃣5⃣",
        '17' => "🎭Анархия",
        '16' => "🥸Вторжение"
    ];

    // Получаем карты из базы данных
    $maps = [];
    $stmt = $pdo->query("SELECT id, name FROM maps");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $maps[$row['id']] = $row['name'];
    }

    foreach ($filtered_output as &$obj) {
        if (isset($obj['Режим']) && isset($mode_mapping[$obj['Режим']])) {
            $obj['Режим'] = $mode_mapping[$obj['Режим']];
        }

        // Проверяем, является ли "Айди или ник" числом
        if (isset($obj['Айди или ник'])) {
            $id_or_nick = $obj['Айди или ник'];
            if (is_numeric($id_or_nick) && isset($maps[$id_or_nick])) {
                // Если это ID, заменяем на название карты
                $obj['Айди или ник'] = $maps[$id_or_nick];
            }
            // Если это не ID, оставляем как есть
        }
    }

    // Сбрасываем ссылки
    unset($obj);

    return array_values($filtered_output);
}

// Пример использования
$host = "localhost";
$dbname = "lilit";
$username = "root";
$password = "R6O_Qdg9scd3aKgL";
$charset = "utf8";

$dsn = "mysql:host=$host;dbname=$dbname;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
];

function format_output($filtered_output)
{
    $message = "Хих) Воть текущий онлайн на серверах 😊";
    $current_mode = "";
    $index = 1;

    foreach ($filtered_output as $game) {
        if ($game["Режим"] !== $current_mode) {
            $current_mode = $game["Режим"];
            $message .= "<br>$current_mode<br>";
        }
        $message .= "{$index}) {$game['Айди или ник']} - {$game['кол-игроков']} / {$game['Макс. игроков']} <br>";
        $index++;
    }

    return $message;
}

// Пример использования
try {
    $pdo = new PDO($dsn, $username, $password, $options);
    $response = file_get_contents('https://blockade3d.com/api_classic/servers/handler.php?NETWORK=1&CMD=4&time=1');
    $result = handle_response($response, $pdo);

    // Форматируем вывод
    $formatted_output = format_output($result);

    // Выводим результат
    echo $formatted_output;
} catch (\PDOException $e) {
    die("Ошибка подключения к базе данных: " . $e->getMessage());
} catch (\Exception $e) {
    die("Ошибка: " . $e->getMessage());
}
?>