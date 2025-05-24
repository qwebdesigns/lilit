import requests
import vk_api
import re
from vk_api.bot_longpoll import VkBotLongPoll, VkBotEventType
from urllib.parse import quote

TOKEN = "vk1.a.F5iBdU5kTcn8IFxrqHHQ1NqUMijqobXX6p9_uWCLb9KmON1YK6vrIJRKaZGspfFnBubENACoMlKRr7KnWqLBe9H3Q4Y5Mm80Xyy1SWNuzlopdfNK8tlkByNqGC9RdDCvvB7yf-Vmuu-KIwfXcPoGUizezWHoWpVQCOyAzNXOzw_7SDO3VcZ2lWpvCysfHGwVVI3qGRiyAq-p4EHSp90JXQ"
GROUP_ID = "230274484"
BASE_URL = "https://77.222.58.245/php/"

vk_session = vk_api.VkApi(token=TOKEN)
vk = vk_session.get_api()
longpoll = VkBotLongPoll(vk_session, GROUP_ID)

PREFIXES = ["лилит", "лили", "л", "котя", "зая", "лися", "т"]
COMMANDS = {
    "карты": {"handler": "handle_maps", "args": False},
    "клан вк": {"handler": "handle_clan", "args": False},
    "клан стим": {"handler": "handle_clan2", "args": False},
    "го": {"handler": "handle_party", "args": False},
    "айди": {"handler": "handle_id", "args": False},
    "участник": {"handler": "handle_player", "args": True},
    "ттх": {"handler": "handle_weapons", "args": True},
    "ттк": {"handler": "handle_weapons_ttk", "args": True},
    "о": {"handler": "handle_weapons", "args": True},
    "профили": {"handler": "handle_profiles", "args": False},
}


class VKBot:
    def __init__(self):
        self.commands_priority = sorted(COMMANDS.keys(), key=len, reverse=True)

    def parse_message(self, text):
        text_lower = text.lower()
        for prefix in PREFIXES:
            if text_lower.startswith(prefix.lower()):
                clean_text = text[len(prefix):].strip()
                if not clean_text:
                    return None

                # Разделяем текст на слова
                words = clean_text.split()
                command_part = words[0]  # Первая часть текста
                for cmd in self.commands_priority:
                    # Проверяем на точное совпадение с помощью регулярного выражения
                    if re.match(rf'^{cmd}\b', command_part):
                        args = ' '.join(words[1:])  # Остальные части текста как аргументы
                        return {"command": cmd, "args": args if args else None}
                return {"command": None, "args": clean_text}
        return None

    def extract_mention(self, text):
        mention = re.findall(r"\[id(\d+)\|.*?\]|@(\w+)", text)
        if mention:
            return mention[0][0] or mention[0][1]
        return None

    def handle_command(self, event, parsed):
        command = COMMANDS.get(parsed["command"])
        if not command:
            return "Неизвестная команда"

        try:
            handler = getattr(self, command["handler"])
        except AttributeError:
            return "Ошибка обработки команды"

        return handler(event, parsed["args"])

    # Обработчики команд
    def handle_maps(self, event, args):
        response = requests.get(f"{BASE_URL}maps.php", verify=False)
        return response.text

    def handle_profiles(self, event, args):
        response = requests.get(f"{BASE_URL}players_ID.php", verify=False)
        return response.text
    
    def handle_clan(self, event, args):
        response = requests.get(f"{BASE_URL}clan.php", verify=False)
        return response.text
    
    def handle_clan2(self, event, args):
        response = requests.get(f"{BASE_URL}clan_steam.php", verify=False)
        return response.text
    
    def handle_party(self, event, args):
        response = requests.get(f"{BASE_URL}party_get.php", verify=False)
        return response.text

    def handle_player(self, event, args):
        user_id = event.obj.message["from_id"]
        #print("айди1 = " + str(user_id) + " id2 = " + str(args))
        if args != None:
            search_id = args
        else: 
            search_id = user_id
        response = requests.get(
            f"{BASE_URL}player_get_bot.php?link={search_id}", verify=False)
        print(f"{BASE_URL}player_get_bot.php?link={search_id}")
        return response.text

    def handle_id(self, event, args):
        user_id = event.obj.message["from_id"]
        # print(user_id)
        if args:
            mention_id = self.extract_mention(args)
            if mention_id:
                user_id = mention_id

        response = 'Твой айди ВК: ' + str(user_id)
        return response

    def handle_weapons(self, event, args):
        if not args:
            return "Укажите название оружия"

        encoded_arg = quote(args.strip().encode("utf-8"))
        response = requests.get(
            f"{BASE_URL}weapons.php?alias={encoded_arg}", verify=False)
        return response.text
    
    def handle_weapons_ttk(self, event, args):
        if not args:
            return "Укажите название оружия"

        encoded_arg = quote(args.strip().encode("utf-8"))
        response = requests.get(
            f"{BASE_URL}weapons_ttk.php?alias={encoded_arg}", verify=False
        )
        return response.text

    def send_message(self, peer_id, message):
        vk.messages.send(
            peer_id=peer_id, message=message, random_id=0, disable_mentions=1
        )

    def run(self):
        for event in longpoll.listen():
            if event.type == VkBotEventType.MESSAGE_NEW:
                msg = event.obj.message
                parsed = self.parse_message(msg["text"])

                if parsed and parsed["command"] in COMMANDS:
                    try:
                        response = self.handle_command(event, parsed)
                        self.send_message(msg["peer_id"], response)
                    except Exception as e:
                        print(f"Ошибка: {str(e)}")
                        self.send_message(
                            msg["peer_id"], "Произошла ошибка при обработке запроса"
                        )


if __name__ == "__main__":
    bot = VKBot()
    bot.run()
