<?php

$modules_list = array(
  
  // админские
  'accounts' => array('title' => "Учетные записи"),
  'admin' => array('title' => "Админцентр"),
  'antispam' => array('title' => "Антиспам-сессии", 'use' => 'scaffold', 'model' => 'antispam'),
  'attaches' => array('title' => "Прикрепленные файлы"),
  'book' => array('title' => "Записи в гостевой"),
  'cache' => array('title' => "Кэшированные данные"),
  'cats' => array('title' => "Категории", 'use' => 'scaffold', 'model' => 'cat'),
  'commentsedit' => array('title' => "Комментарии"),
  'config' => array('title' => "Настройки сайта"),
  'daystats' => array('title' => "Дневная статистика"),
  'infoblocks' => array('title' => "Инфоблоки"),
  'logs' => array('title' => "Просмотр логов", 'use' => 'scaffold', 'model' => 'logrecord'),
  'news' => array('title' => "Новостная лента"),  
  'pages' => array('title' => "Структура сайта"),  
  'polls' => array('title' => "Голосования", 'use' => 'scaffold', 'model' => 'poll'),
  'press' => array('title' => "Пресс-релизы и СМИ"),
  'restores' => array('title' => "Восстановления паролей"),  
  'rssdata' => array('title' => "Новости с RSS каналов"),
  'searchlogs' => array('title' => "Поисковые запросы"),
  'subscribers' => array('title' => "Подписчики"),
  'urls' => array('title' => "Адреса страниц сайта"),  
  'video' => array('title' => "Видео"),
  'sessions' => array('title' => "Сессии", 'use' => 'scaffold', 'model' => 'session'),
  'searches' => array('title' => "Поисковые запросы", 'use' => 'scaffold', 'model' => 'search'),
  'searchresults' => array('title' => "Результаты поисков", 'use' => 'scaffold', 'model' => 'searchresult'),  
  
  'advstreams' => array('title' => "Рекламные потоки"),
  'advblocks'  => array('title' => "Рекламные блоки"),
  'advshow'    => array('title' => "Настройка показов рекламы"),  
  
  // стандартные публичные
  'page' => array('title' => 'Обычная страница', "public" => 1),
  'auth' => array('title' => 'Страница входа'),
  'faq' => array('title' => "FAQ", 'public' => 1),
  'guestbook' => array('title' => "Гостевая книга", 'public' => 1),
  'log' => array('title' => "Авторизация", 'public' => 1),
  'mail' => array('title' => "Личные сообщения", "public" => 1),
  'main' => array('title' => "Главная страница", "public" => 1),
  'newsline' => array('title' => "Новостная лента", 'public' => 1),
  'polllist' => array('title' => "Архив голосований", 'public' => 1),
  'profile' => array('title' => "Профиль пользователя", 'public' => 1),
  'reminder' => array('title' => "Восстановление пароля", "public" => 1),
  'registration' => array('title' => "Регистрация", 'public' => 1),
  'sitemap' => array('title' => "Карта сайта", 'public' => 1),
  'subscribe' => array('title' => "Подписка", 'public' => 1),  
  'tools' => array('title' => "Вспомогательные функции", 'public' => 1),
  'errpage' => array('title' => "Сообщение об ошибке.", 'public' => 1),
  'search' => array('title' => 'Поиск по сайту', 'public' => 1),  
  'download' => array('title' => 'Скачивание файла', 'public' => 1),
  
  // афиша
  'halls' => array('title' => "Площадки"),
  'events' => array('title' => "Мероприятия/фильмы"),
  
  // магазин
  'products' => array('title' => "Товары"),
  'orders' => array('title' => "Заказы", 'use' => 'scaffold', 'model' => 'order'),
  'basketedit' => array('title' => "Товары в корзинах", 'use' => 'scaffold', 'model' => 'basket'),
  'catalog' => array('title' => "Каталог товаров", 'public' => 1),
  'basket' => array('title' => "Корзина товаров", 'public' => 1),
  'order' => array('title' => "История заказов", 'public' => 1),  
  'wmresult' => array('title' => "Результат WM платежа", 'public' => 1),  
  'wmresults' => array('title' => "Логи WM платежей"),

  // форум
  'forums' => array('title' => "Редактор форумов", 'use' => 'scaffold', 'model' => 'forum'),
  'fcats' => array('title' => "Категории форумов", 'use' => 'scaffold', 'model' => 'fcat'),
  'ftopics' => array('title' => "Темы", 'use' => 'scaffold', 'model' => 'ftopic'),
  'fposts' => array('title' => "Ответы", 'use' => 'scaffold', 'model' => 'fpost'),
  'forum' => array('title' => "Форум", 'public' => 1),  
  'topic' => array('title' => "Просмотр темы на форуме", 'public' => 1),
  'post' => array('title' => "Сообщение на форуме", 'public' => 1),
  
  //доска объявлений
  'bcats' => array('title' => "Категории доски объявлений"),  
  
  'garden' => array('title' => "Садовая мебель", 'public' => 1),
  'gardenedit' => array('title' => "Садовая мебель", 'use' => 'scaffold', 'model' => 'garden'),
  
  //одиночный блог
  'blogpostedit' => array('title' => "Сообщения в блоге", 'use' => 'scaffold', 'model' => 'blogpost'),
  'blog' => array('title' => "Модуль обычного блога на сайте", 'public' => 1),
  'trustemails' => array('title' => "Трастовые почтовые адреса", 'use' => 'scaffold', 'model' => 'trustemail'),
  'feededit' => array('title' => "RSS ленты", 'use' => 'scaffold', 'model' => 'feed'),
  'feed' => array('title' => "Выдача RSS лент", 'public' => 1),
  
  // каталог сайтов
  'sitecatalog' => array('title' => "Каталог сайтов", 'public' => 1),
  'siteedit' => array('title' => "Каталог сайтов", 'public' => 1),
    
);


$modules_access = array(
  
//guests
0 => array(
'auth',
'log',
'main',
'newsline',
'errpage',
'polllist',
'reminder',
'registration',
'search',
'sitemap',
'tools',
'download',
'feed'
),

// registred (base)
1 => array(
'log',
'main',
'newsline', 
'errpage',
'polllist',
'profile',
'registration',
'search',
'sitemap',
'tools',
'download',
),

2 => array(), 

3 => array(
  'accounts',
  'admin',
  'antispam',
  'attaches',
  'book',
  'cache',
  'commentsedit',
  'config',
  'halls',
  'infoblocks',
  'logs',
  'news',
  'pages',
  'restores',
  'searches',
  'searchresults',   
  'sessions',
  'subscribers',
  'urls',
  
'blogpostedit',
'blog',  
'trustemails',
'feededit',
'feed'  
)

);


$base_access = $modules_access[1];

/*
все группы выше зарегистрированных пользователей имеют доступ к функциям зарегистрированных пользователей
*/
foreach($modules_access as $gid => $ar) {
  if($gid<>0 && $gid<>1) {
    foreach($base_access as $act) {
      $modules_access[$gid][] = $act;
    }
  }  
}



?>