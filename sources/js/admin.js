$(document).ready(function(){
    $("#go").click(function(){
  $(".block").animate( { backgroundColor: 'pink' }, 1000)
    .animate( { backgroundColor: 'blue' }, 1000);
});
});
  


$(document).ready(function(){
  $(".tab").click(function(){   
    $(".tab").removeClass('here');
     $(this).addClass('here');
  });
});


function select_part(name) {
  $(".part").hide();
  $("#"+name).show();
  
}

function markitup_insert(open, close, holder) {
  $.markItUp(
  		{ openWith: open,
  		  closeWith: close,
  		  placeHolder: holder }
  	);
}     
