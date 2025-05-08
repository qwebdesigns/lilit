var b12 = document.getElementById("btn-edit");
var b22 = document.getElementById("btn-create");

function resetButtonColors() {
  b12.style.backgroundColor = "#7a4fff";
  b22.style.backgroundColor = "#7a4fff";
}

function edit_profile(button) {
  document.getElementById("profiles-form").action =
    "https://77.222.58.245/php/player_old.php";

  document.getElementById("vk-id").addEventListener("change", function () {
    fetchPlayerData(this.value);
  });

  resetButtonColors(); // Сбрасываем цвет перед изменением
  button.style.backgroundColor = "#a892ed";
}

function new_profile(button) {
  document.getElementById("profiles-form").action =
    "https://77.222.58.245/php/player_new.php";

  const vkIdInput = document.getElementById("vk-id");

  if (typeof fetchPlayerData === "function") {
    vkIdInput.removeEventListener("change", function () {
      fetchPlayerData(this.value);
    });
  }

  resetButtonColors(); // Сбрасываем цвет перед изменением
  button.style.backgroundColor = "#a892ed";
}

//action="http://localhost/lilit/player_new.php"
// Tab switching logic
const buttons = document.querySelectorAll("aside button[data-tab]");
const sections = document.querySelectorAll("main section[data-content]");

buttons.forEach((btn) => {
  btn.addEventListener("click", () => {
    const target = btn.getAttribute("data-tab");
    buttons.forEach((b) => b.classList.remove("text-white"));
    btn.classList.add("text-white");
    sections.forEach((sec) => {
      if (sec.getAttribute("data-content") === target) {
        sec.classList.remove("hidden");
      } else {
        sec.classList.add("hidden");
      }
    });
  });
});

// Initialize first tab as active
buttons[0].classList.add("text-white");
sections.forEach((sec, i) => {
  if (i !== 0) sec.classList.add("hidden");
});

// Add profile entry logic
const addProfileBtn = document.getElementById("add-profile");
const profilesContainer = document.getElementById("profiles-container");

addProfileBtn.addEventListener("click", () => {
  const count = profilesContainer.children.length;
  const div = document.createElement("div");
  div.className =
    "profile-entry space-y-4 border border-[#3a3f4d] rounded-md p-4";
  div.innerHTML = `
        <div>
          <label class="block mb-2 text-sm font-medium" for="level-${count}">Уровень</label>
          <input
            type="number"
            id="level-${count}"
            name="level[]"
            min="0"
            class="w-full rounded-md bg-[#2a2e3a] border border-[#3a3f4d] px-4 py-2 text-white placeholder-[#6e7280] focus:outline-none focus:ring-2 focus:ring-[#7a4fff]"
            placeholder="Введите уровень"
          />
        </div>
        <div>
          <label class="block mb-2 text-sm font-medium" for="nickname-${count}">Никнейм</label>
          <input
            type="text"
            id="nickname-${count}"
            name="nickname[]"
            class="w-full rounded-md bg-[#2a2e3a] border border-[#3a3f4d] px-4 py-2 text-white placeholder-[#6e7280] focus:outline-none focus:ring-2 focus:ring-[#7a4fff]"
            placeholder="Введите никнейм"
          />
        </div>
      `;
  profilesContainer.appendChild(div);
});

// Show/hide form on button click in Game Profiles tab
const btnEdit = document.getElementById("btn-edit");
const btnCreate = document.getElementById("btn-create");
const profilesForm = document.getElementById("profiles-form");

btnEdit.addEventListener("click", () => {
  profilesForm.style.display = "block";
});
btnCreate.addEventListener("click", () => {
  profilesForm.style.display = "block";
});
