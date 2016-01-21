переносим в гит


Установка, желательно в этом же порядке:
1. path_cfg - прописываем правильные пути в path_cfg.php и свой айпишник в $trace_ips
2. mysql_cfg - прописываем данные подключения к базе и нужные таблицы в mysql_cfg.php
3. запускаем index.php?install - при этом создаются все доступные таблицы выбранные в mysql_cfg
4. разрешаем в modules_list.php доступ группе 0 к модулю accounts, заходим в него /admin/?accounts и создаем учетку админа, 
   после создания запрещаем доступ к модулю и авторизаемся в админке.


Настройка счетчика:
1. вставляем в шаблон код: <img src='/sources/counter/counter.php' width='1' height='1' border='0'>
2. после того как заработает меняем на $installed = 1 в counter_config.php





Реклама
m_advstream - advstreams
m_advblock - advblocks
m_advshow - advshow


$html = $sv->view->fetch();

//реклама
$sv->load_model('advstream');
$html = $sv->m['advstream']->replace($html);

echo $html;

