import requests
import re
from urllib.parse import quote
from telegram import Update
from telegram.ext import ApplicationBuilder, MessageHandler, filters, ContextTypes


TOKEN = "8101722945:AAEppf9IJUZdzDZYQXc_36bfM41x6iqsEGY"
BASE_URL = "https://77.222.58.245/php/"


PREFIXES = ["лилит", "лили", "л", "котя", "зая", "лися", "залупа коня"]
COMMANDS = {
    "карты": {"handler": "handle_maps", "args": False},
    "клан": {"handler": "handle_clan", "args": False},
    "го": {"handler": "handle_party", "args": False},
    "айди": {"handler": "handle_id", "args": False},
    "участник": {"handler": "handle_player", "args": True},
    "ттх": {"handler": "handle_weapons", "args": True},
    "о": {"handler": "handle_weapons", "args": True},
}


class TelegramBot:
    def __init__(self, token):
        self.application = ApplicationBuilder().token(token).build()
        self.commands_priority = sorted(COMMANDS.keys(), key=len, reverse=True)

        # Обработчики сообщений
        self.application.add_handler(
            MessageHandler(filters.TEXT & ~filters.COMMAND, self.handle_message)
        )

    def parse_message(self, text):
        text_lower = text.lower()
        for prefix in PREFIXES:
            if text_lower.startswith(prefix.lower()):
                clean_text = text[len(prefix) :].strip()
                for cmd in self.commands_priority:
                    if clean_text.lower().startswith(cmd.lower()):
                        args = clean_text[len(cmd) :].strip()
                        return {"command": cmd, "args": args if args else None}
                return {"command": None, "args": clean_text}
        return None

    def extract_mention(self, text):
        mention = re.findall(r"\[id(\d+)\|.*?\]|@(\w+)", text)
        if mention:
            return mention[0][0] or mention[0][1]
        return None

    def handle_command(self, update: Update, parsed):
        command = COMMANDS.get(parsed["command"])
        if not command:
            return "Неизвестная команда"

        try:
            handler = getattr(self, command["handler"])
        except AttributeError:
            return "Ошибка обработки команды"

        return handler(update, parsed["args"])

    # Обработчики команд
    def handle_maps(self, update: Update, args):
        response = requests.get(f"{BASE_URL}maps.php", verify=False)
        return response.text

    def handle_clan(self, update: Update, args):
        response = requests.get(f"{BASE_URL}clan.php", verify=False)
        return response.text

    def handle_party(self, update: Update, args):
        response = requests.get(f"{BASE_URL}party_get.php", verify=False)
        return response.text

    def handle_player(self, update: Update, args):
        user_id = update.message.from_user.id
        if args:
            mention_id = self.extract_mention(args)
            if mention_id:
                user_id = mention_id

        response = requests.get(
            f"{BASE_URL}player_get_bot.php?link={user_id}", verify=False
        )
        return response.text

    def handle_id(self, update: Update, args):
        user_id = update.message.from_user.id
        # print(user_id)
        if args:
            mention_id = self.extract_mention(args)
            if mention_id:
                user_id = mention_id

        response = "Твой айди ТГ: " + str(user_id)
        return response

    def handle_weapons(self, update: Update, args):
        if not args:
            return "Укажите название оружия"

        encoded_arg = quote(args.strip().encode("utf-8"))
        response = requests.get(
            f"{BASE_URL}weapons.php?alias={encoded_arg}", verify=False
        )
        return response.text

    async def handle_message(self, update: Update, context: ContextTypes.DEFAULT_TYPE):
        parsed = self.parse_message(update.message.text)

        if parsed and parsed["command"] in COMMANDS:
            try:
                response = self.handle_command(update, parsed)
                # Replace <br> with newline before sending
                if response:
                    response = response.replace("<br>", "\n")
                await update.message.reply_text(response)
            except Exception as e:
                logger.error(f"Ошибка: {str(e)}")
                await update.message.reply_text(
                    "Произошла ошибка при обработке запроса"
                )

    def run(self):
        self.application.run_polling()


if __name__ == "__main__":
    bot = TelegramBot(TOKEN)
    bot.run()
