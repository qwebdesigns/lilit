function fetchPlayerData(link) {
  fetch(
    `https://77.222.58.245/php/player_get_bot.php?link=${encodeURIComponent(
      link
    )}`
  )
    .then((response) => {
      if (!response.ok) {
        throw new Error("Network response was not ok");
      }
      return response.text();
    })
    .then((text) => {
      console.log("Raw response text:", text); // Отладка: выводим сырой ответ

      if (text.includes("Профиль не найден(")) {
        console.log("Профиль не найден(");
        return;
      }

      const data = {};
      const lines = text.replace(/<br\s*\/?>/gi, "\n").split("\n");

      // Обработка профилей
      const profiles = [];
      for (const line of lines) {
        const cleanLine = line.trim();
        if (!cleanLine) continue; // Пропускаем пустые строки

        // Проверяем, если строка содержит профиль
        const profileData = cleanLine.match(/\[(\d+)\]\s*(.*)/);
        if (profileData) {
          const level = profileData[1];
          const nickname = profileData[2].trim();
          profiles.push({ level, nickname });
        } else {
          // Обычные ключи
          const keyValuePair = cleanLine.match(/([^:]+):\s*(.*)/);
          if (keyValuePair) {
            const key = keyValuePair[1].trim();
            const value = keyValuePair[2].trim();
            data[key] = value;
          }
        }
      }

      // Дополнительные данные
      data.profiles = profiles;

      const jsonData = JSON.stringify(data, null, 2);
      console.log("Final JSON data:", jsonData); // Отладка: выводим финальный JSON

      // Заполняем форму
      fillForm(jsonData);
    })
    .catch((error) => {
      console.error("Ошибка:", error);
    });
}

// Функция для заполнения формы
function fillForm(jsonData) {
  const formData = JSON.parse(jsonData);

  // Очистка предыдущих профилей
  profilesContainer.innerHTML = "";

  // Добавление профилей
  formData.profiles.forEach((profile, index) => {
    const div = document.createElement("div");
    div.className =
      "profile-entry space-y-4 border border-[#3a3f4d] rounded-md p-4";
    div.innerHTML = `
      <div>
        <label class="block mb-2 text-sm font-medium" for="level-${index}">Уровень</label>
        <input
          type="number"
          id="level-${index}"
          name="level[]"
          min="0"
          class="w-full rounded-md bg-[#2a2e3a] border border-[#3a3f4d] px-4 py-2 text-white placeholder-[#6e7280] focus:outline-none focus:ring-2 focus:ring-[#7a4fff]"
          placeholder="Введите уровень"
          value="${profile.level}"
        />
      </div>
      <div>
        <label class="block mb-2 text-sm font-medium" for="nickname-${index}">Никнейм</label>
        <input
          type="text"
          id="nickname-${index}"
          name="nickname[]"
          class="w-full rounded-md bg-[#2a2e3a] border border-[#3a3f4d] px-4 py-2 text-white placeholder-[#6e7280] focus:outline-none focus:ring-2 focus:ring-[#7a4fff]"
          placeholder="Введите никнейм"
          value="${profile.nickname}"
        />
      </div>
    `;
    profilesContainer.appendChild(div);
  });

  // Заполнение других полей
  document.getElementById("main-game-id").value = formData["🔖 ID"] || "";
  document.getElementById("vk-id").value = formData["Ссылка"] || "";
  document.getElementById("fav-weapon").value =
    formData["🔫 Любимое оружие"] || "";

  // Очистка значения для соцсетей
  const socials = formData["🌐 Соцсети"] || "";
  const cleanedSocials = socials
    .replace(/<[^>]*>/g, "") // Удаляем HTML-теги
    .replace(/&nbsp;/g, " ") // Заменяем HTML-сущность на пробел
    .trim(); // Обрезаем пробелы

  document.getElementById("socials").value = cleanedSocials;

  // Очистка значения для описания
  const description = formData["📝 Описание"] || "";
  const cleanedDescription = description
    .replace(/<[^>]*>/g, "") // Удаляем HTML-теги
    .replace(/&nbsp;/g, " ") // Заменяем HTML-сущность на пробел
    .trim(); // Обрезаем пробелы

  document.getElementById("description").value = cleanedDescription;

  // Очистка значения для любимых карт
  const favoriteMaps = formData["🗺️ Любимые карты"] || "";
  const cleanedMaps = favoriteMaps
    .replace(/<[^>]*>/g, "") // Удаляем HTML-теги
    .replace(/&nbsp;/g, " ") // Заменяем HTML-сущность на пробел
    .trim(); // Обрезаем пробелы

  document.getElementById("fav-map").value = cleanedMaps;

  document.getElementById("avg-kd").value = formData["📊 Средний K/D"] || "";
  document.getElementById("awards").value = formData["🏆 Награды"] || "";
  document.getElementById("title").value = formData["🎖️ Титул"] || "";
}
