<script>
/*
   Script name  : PHP and Ajax Comment
   File Name 	: script.js
   Developed By : Amit Patil (India)
   Email Id 	: amitpatil321@gmail.com
   Date Created : 21 June 2009
   Last Updated : 24 Aug 2009
         This program is freeware.There is no any fucking copyright and bla bla bla.
   You can use it for your personal use.You can also make any changes to this script.
   But before using this script i would appericiate your mail.That will encourage me a lot.
   Any suggestions are always welcome.
         Have a fun with PHP.   
*/

var id=0;
var bpartido=false;
function setIdComentarios(v){
	id=v;
}
function setBPartido(v){
	bpartido=v;
}

function loadCoreComments(){
  $("#comment_table"+id).find('textarea, input:text').blur(function () {
	if ($(this).val() != ''){
		$(this).removeClass("required");
	}
  });	
  $("#submit"+id).click(function(){
	  var anyBlank = 0;
	  setIdComentarios(this.id.substring(6))
	  $("#comment_table"+id).find('textarea, input:text').each(function () {
		
		if (this.id!="email"+id && $(this).val() == ''){
			$(this).addClass("required");
			anyBlank = 1;
		}
	  });	
	  if(anyBlank == "0")
	  {
		  var name    = $("#name"+id).val();
		  var email   = $("#email"+id).val();
		  var comment = $("#comment"+id).val();
		  comment = comment.replace(/\n\r?/g, '<br />');
		  $("#loading"+id).css("visibility","visible");
			$.ajax({
			   type: "POST",
			   url: "<?=getServer()?>/assets/ajax_comment.php",
			   data: "name="+escape(name)+"&email="+escape(email)+"&comment="+escape(comment)+"&p="+bpartido+"&i="+id,
			   success: function(date_added){
				  if(date_added != 0)
				   {
					   structure = '<div class="comment_holder"><div id="comment_name">'+name+'</div><div id="date_posted">'+date_added+'</div><div>'+comment+'</div></div></div>';				  	
					   $(".no_comments").fadeOut("slow");
					   document.getElementById('ajax_response'+id).innerHTML=structure+document.getElementById('ajax_response'+id).innerHTML;
					   //$("#ajax_response").prepend(structure);
					   $("#comment_table"+id).find('textarea, input:text').each(function () {
							if (this.id!="email"+id)
								$(this).val("");
					   });
				   }
				  else
					  alert("Unexpected error...!");
  					  $("#loading").css("visibility","hidden");
			   }
			 });
	  }
  });
}
</script>