<?php
$db_init_vars['host']			=	'localhost';
$db_init_vars['user']			=	'gorodok';
$db_init_vars['pass']			=	'';
$db_init_vars['dbname']		=	'gorodok';
$db_init_vars['prefix']		=	'';
$db_init_vars['debug']		=	'1';
$db_init_vars['charset']	=	'';
$db_init_vars['log_changes'] = 0;


$db_init_vars['tables'] = array(

  'accounts',   // !учетные записи
  'antispam',   // сессии капчей
  'attaches',   // !стандартные аттачи для струтуры сайта и новостей
  'cache',      // !кэш
  'comments',
  'config',     // !настройки
  'infoblocks', // !инфоблоки  
  'logs',       // !логи авторизаций
  'news',       // !новостная лента
  'pages',      // !структура сайта
  'page_parts', // !контент структуры сайта
  'restore',    // !сессии восстановления пароля
  'searches',
  'searchresults',
  'sessions',   // !сессии пользователей
  'subscribers',// подписчики
  'urls'        // !адреса страниц сайта

);
?>