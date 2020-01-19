# [Payamresan](https://t.me/prackbot)

[![Bot API](http://img.shields.io/badge/Bot%20API-v3.5-00aced.svg)](https://core.telegram.org/bots/api)
[![https://t.me/Csapktvu](https://img.shields.io/badge/💬%20Telegram-Csapktvu-00aced.svg)](https://t.me/Csapktvu)

## Prackbot V1.0
 Scientific Societies messenger Telegram-bot to connect with members 
* * *

## Configure

* Put Your Bot `TOKEN` At Line `2`
* Put Your `Telegram ID` At Line `115`

# Installation

That being said, the quickest and easiest way to set a WebHook for your Bot is to issue a GET request to the Bot API (it’s enough to open an url in your browser).

All you have to do is to call the setWebHook method in the Bot API via the following url:

```
https://api.telegram.org/bot{my_bot_token}/setWebhook?url={url_to_send_updates_to}
```
where
my_bot_token is the token you got from BotFather when you created your Bot
url_to_send_updates_to is the url of the piece of code you wrote to implement your Bot behavior (must be HTTPS)
For instance:
```
https://api.telegram.org/bot123456:ABC-DEF1234ghIkl-zyx57W2v1u123ew11/setWebhook?url=https://www.example.com/my-telegram-bot
```
And you’ve got it.
Now if you go to the following url (you have to replace {my_bot_token} with your Bot Token)
```
https://api.telegram.org/bot{my_bot_token}/getWebhookInfo
```
you should see something like this:
```
{
 "ok":true,
 "result": 
 {
   "url":"https://www.example.com/my-telegram-bot/",
   "has_custom_certificate":false,
   "pending_update_count":0,
   "max_connections":40
 }
}
```
For a complete list of parameters for the setWebHook method have a look at the official API reference.
