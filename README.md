# ftr
Открытый форум повторяющий простоту и функциональность форумов forum.tomsk.ru и mozg.tomsk.ru.

# Установка Ubuntu 18.04

Установите следующие пакеты

  apt install lighttpd php-cgi php-sqlite3 php-mbstring

Дополнительно может установиться веб сервер Apache, сносим:

  apt purge apache2
  
  apt autoremove

Настраиваем моды для вебсервера

  lighttpd-enable-mod fastcgi-php fastcgi

  systemctl restart lighttpd

Создаем директорию для базы данных

  mkdir /var/lib/antimozg

  chown www-data:www-data /var/lib/antimozg

Копируем исходники форума в директорию /var/www/html

Для заливки картинок устанавливаем следующие пакеты

  apt install imagemagick netpbm libjpeg-progs

Разрешаем запись в директорию

  chmod www-data:www-data /var/www/html/uploads


