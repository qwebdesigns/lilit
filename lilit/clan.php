
<?php


// URL для запроса
$url = "https://blockade3d.com/api_classic/handler.php?NETWORK=1&CMD=1000&id=123974629&key=c2733bd835b0db5c5d34b9d17133120e&session=&time=57";

$num = 9;
// Инициализация cURL
$ch = curl_init($url);

// Установка параметров cURL
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json; charset=utf-8'));

// Выполнение запроса
$response = curl_exec($ch);

// Проверка на ошибки
if (curl_errno($ch)) {
    echo 'Ошибка cURL: ' . curl_error($ch);
} else {
    // Декодирование JSON
    $data = $response;
    $split_data = explode('^', $data);
    $clan_info = explode('|', $split_data[0]);
    $dry_data = explode('|', $split_data[1]);
    $slicedArray = array_slice($dry_data, 0, $num);
    $jsonData = array_map(function ($item) {
        $parts = explode("*", $item);
        return [
            'nickname' => $parts[0] ?? null,
            'profileXP' => $parts[1] ?? null,
            'id' => $parts[2] ?? null,
            'currentTimestamp' => $parts[3] ?? null,
            'dailyXP' => $parts[4] ?? null,
            'totalXP' => $parts[5] ?? null,
            'authorityID' => $parts[6] ?? null
        ];
    }, $slicedArray);


    echo "❤️ Воть информация по клану :3 (ВК) <br>";
    echo "🏷️Название: " . $clan_info[0] ."<br>";
    echo "🔥Текущий опыт: " . $clan_info[1]."<br>";
    echo "👥Участников: ". count($dry_data) . " \\ ". $clan_info[3]."<br>";
    echo "👑Владелец: " . $clan_info[4]."<br>";
    echo "📋Текущих список первых " . $num . " участников <br>";
    foreach ($jsonData as $index => $participant) {
        $authorityEmojis = [
            1 => "⚔️",
            2 => "💼",
            3 => "🤝",
            4 => "👑"
        ];

        echo "#" . ($index + 1) . " " . htmlspecialchars($participant['nickname']) . " (" . htmlspecialchars($participant['id']) . ") " .
            $authorityEmojis[$participant['authorityID']] . "<br>🚩 Опыт: " . htmlspecialchars($participant['dailyXP']) . " | " . htmlspecialchars($participant['totalXP']) . "<br>⌛ Актив: " . date("d.m.Y", htmlspecialchars($participant['currentTimestamp'])) . '<br>';
    }
    echo ' ';
    echo 'Если будут дополнительные вопросы, я всегда готова помочь! 😉';


};







/*
let bodyText = document.body.innerText;
let splitText = bodyText.split('^'); // Разделение текста по "^"

let firstData = splitText[0].split("|"); // Разделение первого блока по "|"
console.warn(firstData); // Вывод массива

let secondData = splitText[1].split("|"); // Разделение второго блока по "|"

let jsonData = secondData.map(item => {
    let parts = item.split("*"); // Разделение по "*"
    return {
        nickname: parts[0],        // Ник
        profileXP: parts[1],       // Текущий опыт профиля
        id: parts[2],              // ID
        currentTimestamp: parts[3], // Текущий таймстамп вместо additionalID
        dailyXP: parts[4],         // Опыт за день
        totalXP: parts[5],         // Всего принёс опыта
        authorityID: parts[6]      // ID власти
    };
});

console.log(JSON.stringify(jsonData, null, 2)); // Вывод JSON в консоль, красиво отформатированный
 */
?>