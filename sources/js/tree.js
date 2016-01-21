/* доработанный pages.js */

var tree_model = '';

// функции для управления коллекцией раскрытых веток, пока что не работает
function page_add(pageid) {
  //alert("добавляем "+pageid);  
}
function page_remove(pageid) {
  //alert("убираем "+pageid);
}

var waiting_div = "<div class='wait' style='display:block;color: gray;'><img src='/i/stuff/wait16.gif' width='16' height='16' border='0' style='vertical-align:-3px;'> загрузка...</div>";


// вешаем обработчик на клики по любым свитчерам, в том числе и подгруженным
$(function() {  
  $('body').click(function(event) {
    if ($(event.target).is('.page-switcher')) {
      page_switch(event.target);
    }
  });
});

// события происходящие по щелчку на ссылке +/-
function page_switch(obj) {
   
    var id = $(obj).attr('id');
    var pageid = id.match("[0-9]+");
    // меняем вид на противолопожный
    var new_title = ($(obj).html()=='-') ? '+' : "-"
    $(obj).html(new_title);

    if (new_title=='+') {
      page_remove(pageid);
      return false;
    }
    else {
      page_add(pageid);
    }
        
    // скрываем/показываем дочерний див
    $("#page-"+pageid+"-childs").toggle('fast');    
    
  
    // смотрим есть ли в нем что-нибудь если нет, то пытаемся подгрузить
    var childs = $("#page-"+pageid+"-childs").html();
    if (childs=='') {
      
      // покзаываем иконку загрузки
      $("#page-"+pageid+"-childs").append(waiting_div); 
      pageid = pageid.toString();
      // посылаем запрос
      $.post("./post.php", {act : "tree_childs",  id : pageid, model : tree_model, module: tree_module}, function (data) {
        // очищаем и добавляем в конец
        $("#page-"+pageid+"-childs").empty();         
        $("#page-"+pageid+"-childs").append(data);          
      });
    }
    else {
      // в диве уже что то есть, а именно:
      //alert('view data');
    }
     
}




$(document).ready(function(){
  $(".node").mouseover(function(){    
    $(this).css("backgroundColor", '#efefef');   
  });
  $(".node").mouseout(function(){    
    $(this).css("backgroundColor", '#ffffff');   
  });  
  
});
