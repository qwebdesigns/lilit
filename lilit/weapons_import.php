<?php
// Логирование
function log_message($message, $type = 'INFO')
{
    $colors = [
        'SUCCESS' => "<p style=\"margin: 0; padding: 0; color: green; font-weight: bold;\">", // Зеленый
        'WARNING' => "<p style=\"margin: 0; padding: 0; color: yellow; font-weight: bold;\">", // Желтый
        'ERROR' => "<p style=\"margin: 0; padding: 0; color: red; font-weight: bold;\">", // Красный
        'INFO' => "<p style=\"margin: 0; padding: 0; color: blue; font-weight: bold;\">", // Синий (для INFO)
    ];
    $reset = "\033[0m"; // Сброс цвета

    if (!isset($colors[$type])) {
        $type = 'INFO'; // Если передано неизвестное значение, устанавливаем INFO
    }

    echo $colors[$type] . "$type: <span style='color:black'>$message<span></p>";
}

// Настройки
ini_set('max_execution_time', 300); // Увеличиваем время выполнения
ini_set('memory_limit', '256M'); // Увеличиваем лимит памяти

// Подключение к базе данных
$host = 'localhost';
$db = 'lilit';
$user = 'root';
$pass = 'R6O_Qdg9scd3aKgL';

log_message('Попытка подключения к базе данных...');

$mysqli = new mysqli($host, $user, $pass, $db);

if ($mysqli->connect_error) {
    log_message('Ошибка подключения: ' . $mysqli->connect_error, 'ERROR');
    die();
} else {
    log_message('Подключение к базе данных успешно!');
}

// URL для запроса
$url = "https://blockade3d.com/api_classic/handler.php?NETWORK=1&CMD=2000&PWD=2b984b3689f5f6f96d65357b6c93c042&API_VERSION=2";

log_message('Попытка выполнения запроса к API...');

// Инициализация cURL
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json; charset=utf-8'));

$response = curl_exec($ch);

if (curl_errno($ch)) {
    log_message('Ошибка cURL: ' . curl_error($ch), 'ERROR');
    curl_close($ch);
    $mysqli->close();
    die();
} else {
    log_message('Запрос к API выполнен успешно!');
}

// Декодирование JSON
log_message('Попытка декодирования JSON...');

$data = json_decode($response, true);

if (json_last_error() !== JSON_ERROR_NONE) {
    log_message('Ошибка декодирования JSON: ' . json_last_error_msg(), 'ERROR');
    curl_close($ch);
    $mysqli->close();
    die();
} else {
    log_message('JSON успешно декодирован!');
}

// Фильтрация данных по типу "ОРУЖИЕ"
log_message('Фильтрация данных...');

$filteredData = array_filter($data, function ($item) {
    return isset($item['item_type']) && $item['item_type']['id'] === "1" && $item['item_type']['name'] === "ОРУЖИЕ";
});

log_message('Количество найденных элементов: ' . count($filteredData));

// Преобразование и вставка/обновление данных в базу
log_message('Обновление данных в базе...');

// Разбиваем данные на части
$chunkSize = 50; // Размер части
$chunks = array_chunk($filteredData, $chunkSize);

foreach ($chunks as $i => $chunk) {
    log_message("Обработка части #{$i} (" . count($chunk) . " элементов)");

    // Начало транзакции
    $mysqli->begin_transaction();

    try {
        foreach ($chunk as $item) {
            $id = $mysqli->real_escape_string($item['item_id']);
            $icon = $mysqli->real_escape_string($item['icon']);
            $type_id = $mysqli->real_escape_string($item['item_type']['id']);
            $type_name = $mysqli->real_escape_string($item['item_type']['name']);
            $category_id = $mysqli->real_escape_string($item['item_category']['id']);
            $category_name = $mysqli->real_escape_string($item['item_category']['name']);
            $item_min_lvl = $mysqli->real_escape_string($item['item_min_lvl']);
            $item_visible_id = $mysqli->real_escape_string($item['item_visible']['id']);
            $item_visible_name = $mysqli->real_escape_string($item['item_visible']['name']);
            $item_theme_id = $mysqli->real_escape_string($item['item_theme']['id']);
            $item_theme_name = $mysqli->real_escape_string($item['item_theme']['name']);
            $item_cost_gold = $mysqli->real_escape_string($item['item_cost_gold']);
            $item_buy_amount = $mysqli->real_escape_string($item['item_buy_amount'] ?? null);
            $item_name_ru = $mysqli->real_escape_string($item['item_name_ru']);
            $item_name_en = $mysqli->real_escape_string($item['item_name_en']);
            $upgrades = isset($item['upgrades']) ? $mysqli->real_escape_string(json_encode($item['upgrades'])) : null;

            $sql = "INSERT INTO items (id, icon, type_id, type_name, category_id, category_name, item_min_lvl, item_visible_id, item_visible_name, item_theme_id, item_theme_name, item_cost_gold, item_buy_amount, item_name_ru, item_name_en, upgrades) 
                    VALUES ('$id', '$icon', '$type_id', '$type_name', '$category_id', '$category_name', '$item_min_lvl', '$item_visible_id', '$item_visible_name', '$item_theme_id', '$item_theme_name', '$item_cost_gold', '$item_buy_amount', '$item_name_ru', '$item_name_en', '$upgrades') 
                    ON DUPLICATE KEY UPDATE 
                        icon='$icon', 
                        type_id='$type_id', 
                        type_name='$type_name', 
                        category_id='$category_id', 
                        category_name='$category_name', 
                        item_min_lvl='$item_min_lvl', 
                        item_visible_id='$item_visible_id', 
                        item_visible_name='$item_visible_name', 
                        item_theme_id='$item_theme_id', 
                        item_theme_name='$item_theme_name', 
                        item_cost_gold='$item_cost_gold', 
                        item_buy_amount='$item_buy_amount', 
                        item_name_ru='$item_name_ru', 
                        item_name_en='$item_name_en', 
                        upgrades='$upgrades'";

            

            if ($mysqli->query($sql) === TRUE) {
                if ($mysqli->affected_rows > 0) {
                    if ($mysqli->affected_rows == 1) {
                        log_message('Добавлено новое оружие: ' . $item_name_ru, 'SUCCESS');
                    } else {
                        log_message('Перезаписано: ' . $item_name_ru, 'SUCCESS');
                    }
                } else {
                    log_message('Ничего не перезаписано: ' . $item_name_ru, 'WARNING');
                }
            } else {
                log_message('Ошибка при обновлении данных: ' . $mysqli->error, 'ERROR');
                throw new Exception('Ошибка при обновлении данных: ' . $mysqli->error);
            }
        }

        // Commit транзакции
        $mysqli->commit();
        log_message("Часть #{$i} успешно обработана!");
    } catch (Exception $e) {
        // Откат транзакции
        $mysqli->rollback();
        log_message('Ошибка при обработке части #' . $i . ': ' . $e->getMessage(), 'ERROR');
        curl_close($ch);
        $mysqli->close();
        die();
    }
}

log_message('Обновление данных завершено!');
curl_close($ch);
$mysqli->close();
log_message('Соединения закрыты!');
?>