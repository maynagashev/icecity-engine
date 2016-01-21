{assign var='d' value=$ar.d}



<h3 style='margin-bottom:5px;'>Сайт &laquo;<span style='color:yellow;'>{$d.title}</span>&raquo;</h3>
<div><small><a href='/'>Назад к списку</a></small></div>


<div style='margin: 20px 0 50px 0;'>
<table cellpadding="5" cellspacing="0" style='margin: 0 0 15px 0;'>
<tr><td>Показов:</td><td>{$d.views}</td></tr>
<tr><td>Переходов:</td><td>{$d.clicks}</td></tr>
<tr><td>Адрес:</td><td><a href='{$d.url_goto}' target="_blank">{$d.url}</a></tr>
</table>
<input type='button' value="Перейти на адрес" onclick="window.location.href='{$d.url_goto}';">

<div style='margin: 20px 10px;'><img src='http://images.websnapr.com/?url={$d.raw_url}&size=m&nocache={$sv->post_time}' width="400" height="300"></div>
</div>