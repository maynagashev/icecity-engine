$(function() {
  
  $("#btn-select-all").click(function(){
    $("input:checkbox").attr("checked", true);
   
  });  
 
  
  $("#btn-unselect-all").click(function(){
    $("input:checkbox").attr("checked", false);
   
  });   
    
});