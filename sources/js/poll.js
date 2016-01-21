


// poll
$(function() {

  
  $("#poll-submit").click(function() {
    var pid = $("#poll-id").val();
    var cid = $("input[@name=poll-cid]:checked").val();   
     $("#active-poll #inputs").hide();
     $("#active-poll #wait").show();
    
    $.getJSON("/post.php",  { act: "poll", pid: pid, cid: cid },  function(json){
      $("#active-poll #wait").hide();          
      $("#active-poll #msg").html(json.errm);
      
      if (json.err) {
        $("#active-poll #inputs").show();
        $("#active-poll #msg").removeClass().addClass('red').fadeIn("slow");
        setTimeout("$('#active-poll #msg').fadeOut();", 2000);
      }
      else {      
        
        // msg
        $("#active-poll #msg").removeClass().addClass('green').fadeIn("slow");
        setTimeout("$('#active-poll #msg').fadeOut();", 2000);
        
        //hide submit
       
        
        // results
        gen_poll_results(json);
           
        // show
        $("#active-poll .choices").slideUp(function(){
            $("#active-poll .results").slideDown('slow');            
        });
        
        
        
      }
    });
    
  });
 
});
 
function gen_poll_results(json) {
 
    var sum = 0;
    $.each( json.list, function(i, d){
       $("#active-poll .results").append(
       "<div class='result-row'><table width=100% cellpadding=0 cellspacing=0>"
       + "<tr><td>"+d.title+"</td><td align='right'>"+d.count+"</td></tr>"
       + "<tr><td><div class='progress' style='width: "+d.w+"px; '></div></td><td align=right><small>"+d.pf+"%</small></td>"
       + "</table></div>");
       sum = d.sum;           
    });
    
    $("#active-poll .results").append(
       "<p align='center'>Всего проголосовало: <b>"+sum+"</b> чел.</p>");  
  
}

