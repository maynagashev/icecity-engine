function basket_add(id) {
  

  $.get("/post.php", {act: 'basket_add', id: id}, function (data) {
    if (data!='') {
      alert(data);
    }
  });
  
  load_basket_content();
  
  return false;
}

function load_basket_content() {
  $("#basket").load("/post.php",  { act: "basket_content"},  function(){
    
  });
    
}