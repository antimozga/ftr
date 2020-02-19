# ftr
Открытый форум повторяющий простоту и функциональность форумов forum.tomsk.ru и mozg.tomsk.ru.

# Installation

apt install lighttpd php-cgi php-sqlite3 php-mbstring



lighttpd-enable-mod fastcgi-php fastcgi

systemctl restart lighttpd



mkdir /var/lib/antimozg

chown www-data:www-data /var/lib/antimozg

apt install imagemagick netpbm libjpeg-progs
