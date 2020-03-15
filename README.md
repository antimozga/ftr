# ftr

Открытый форум повторяющий простоту и функциональность форумов forum.tomsk.ru и mozg.tomsk.ru.

# Установка в Ubuntu Server 18.04

Установите следующие пакеты:

```
apt install lighttpd php-cgi php-sqlite3 php-mbstring php-dom
```

Дополнительно на кривых VPS может установиться веб сервер Apache, сносим:

```
  apt purge apache2
  
  apt autoremove
```

Настраиваем моды для вебсервера:

```
  lighttpd-enable-mod fastcgi-php fastcgi rewrite

  systemctl restart lighttpd
```

Создаем директорию для базы данных:

```
  mkdir /var/lib/antimozg

  chown www-data:www-data /var/lib/antimozg
```

Копируем исходники форума в директорию `/var/www/html`

Расположение базы данных можно изменить в файле конфигурации `/var/www/html/config.php`

Создаем файл настройки для Google Recaptcha V3:

```
nano /var/www/html/config_user.php
```

Устанавливаем ключи:

```
<?php

$RECAPTCHA_SITE_KEY = 'ВАШ SITE KEY';

$RECAPTCHA_SERV_KEY = 'ВАШ SECRET KEY';

?>
```

Ограничиваем права:

```
chmod 600 /var/www/html/config_user.php
```

Примечание:

Для отключения рекапчи в целях отладки, измените ```$debug``` на ```true``` в начале файла ```index.php```

Для заливки картинок устанавливаем следующие пакеты

```
  apt install imagemagick netpbm libjpeg-progs webp
```

Разрешаем запись в директорию

```
  chmod www-data:www-data /var/www/html/uploads
```

Переходим на страницу форума http://адрес-сервера/. Если все сделали правильно, в браузере отобразится главная страница форума. Теперь надо зарегистрировать нового пользователя. После регистрации, редактируем в файл `config.php`:

```
nano /var/www/html/config.php
```

Исправляем строку с администратором форума:

```
$FORUM_ADMIN = "antimozga"; // ник пользователя администратора
```

Заходим на форум под зарегистрированным пользователем. В верхнем меню будет доступен Редактор групп тем. В нем создаем нужные нам на форуме группы тем. Первая созданная тема на форуме будет правилами форума. Изменить можно в `config.php`.

## Мусорная группа

Некоторые темы не обязательно удалять, можно переместить их в мусорную группу, где они будут жить до полного удаления

### Настройка

Создайте группу в которой будут жить перемещенные группы. Запомните id (номер) созданной группы и в `config.php` исправьте на этот номер:

```
$FORUM_TRASH_GID = "30"
```

По умолчанию отключен показ тем из мусорной группы. Чтобы включить, справьте значение false на true в файле `config.php`:

```
$SHOW_TRASH_TOPICS = true;
```

### Увеличение лимита на заливаемые файлы (картинки)

В файлах конфигурации

```
/etc/php/7.2/cgi/php.ini
/etc/php/7.2/cli/php.ini
```

исправьте размер для ```upload_max_filesize```

```
upload_max_filesize = 3M
```

## Настройка сертификата Let's Encrypt

Установите пакет certbot:

```
apt install certbot
```

Запустите certbot и выполните инструкции:

```
certbot certonly --agree-tos --manual -d "*.vtomske.net" -d vtomske.net
```

Настройте права доступа:

```
chown :www-data /etc/letsencrypt
chown :www-data /etc/letsencrypt/live
chmod g+x /etc/letsencrypt
chmod g+x /etc/letsencrypt/live

cat /etc/letsencrypt/live/vtomske.net/privkey.pem /etc/letsencrypt/live/vtomske.net/cert.pem >/etc/letsencrypt/live/vtomske.net/merged.pem
```

Добавьте следующие строки в конец файла конфигурации `/etc/lighttpd/lighttpd.conf`

```
$SERVER["socket"] == ":443" {
    ssl.engine              = "enable"
    ssl.ca-file             = "/etc/letsencrypt/live/vtomske.net/chain.pem"
    ssl.pemfile             = "/etc/letsencrypt/live/vtomske.net/merged.pem"
}

$HTTP["scheme"] == "http" {
    $HTTP["host"] =~ ".*" {
        url.redirect = (".*" => "https://%0$0")
    }
}
```

Перезапустите вебсервер:

```
systemctl restart lighttpd
```

## Weather

Add to ```/etc/crontab```:

```
*  *    * * *   www-data php /var/www/html/weather-gismeteo-cron.php
```
