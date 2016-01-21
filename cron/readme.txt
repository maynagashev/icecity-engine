При первичной настройке необходимо указать в index.php пути до кнфигов и рабочую папку:
$root_dir = "/host/www/n69/public/";
$cron_dir = "/host/www/n69/cron/";
$modules_dir = "./sources/modules/";


Возможные параметры вызова интерпритатора:
#!/usr/local/php5/bin/php

мастерхост шаред
#!/usr/local/php5/bin/php-cli 

#!/usr/local/php5/bin/php -d safe_mode=off
#!/usr/bin/php -q 

Вызов определенного модуля крона
/host/www/n69/cron/index.php tt


// крон
минуты — число от 0 до 59
часы — число от 0 до 23
день месяца — число от 1 до 31
номер месяца в году — число от 1 до 12
день недели — число от 0 до 7 (0-Вс,1-Пн,2-Вт,3-Ср,4-Чт,5-Пт,6-Сб,7-Вс)

минуты часы день месяц день_недели
0 * * * * /home/u149809/admin.ivbanks.ru/www/cron/index.php hourly
5 0 * * * /home/u149809/admin.ivbanks.ru/www/cron/index.php daily


/home/u149809/admin.ivbanks.ru/www/cron/index.php currency
