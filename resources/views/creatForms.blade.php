<link href="{{ asset('assets/css/styles.css') }}" rel="stylesheet">
<div id="mySidenav" class="sidenav">
  
  <a href="#" class='dragme' id="textbox">Textbox</a>
  
  <a href="#" class='dragme' id="date">Date</a>
  <a href="#" class='dragme' id="fileupload">File Upload</a>
  <a href="#" class='dragme' id="button">Button</a>
</div>

<div  id='msgdiv' style="display:none">
   <center id='msg'>
    <i class="fa fa-info-circle"></i>
    </center>
</div>

<div id="Container" >
    <!-- this is Dynamic area -->     
    <p id='clearMe'>Drop here</p>
</div>

<input type="Submit" name="submit" id="submit" style="top: 95%;left: 300px;position: absolute;">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap-theme.min.css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
<script src="https://code.jquery.com/ui/jquery-ui-git.js"></script>
<script>
$(function() {

    var textbox_i=0;
    var button_i=0;
    var date_i=0;
    var fileupload_i=0;
    var imgIds=[];
    $(".dragme").draggable({
        revert: "valid"
    });

    $("#Container").droppable({
        drop: function(event, ui) {

            var regex =/^[a-zA-Z0-9]+[_][a-zA-Z0-9]+$/;

            if(regex.test($(ui.draggable).attr("id")))
                return;

            if(textbox_i+button_i+fileupload_i+date_i==0)
                $("#clearMe").html("");
            
           // console.log($(ui.draggable).attr("id"));
            
            switch($(ui.draggable).attr("id"))
            {
                case "textbox":
                {

                    textbox_i++;
                    var id=$(ui.draggable).attr("id")+"_"+textbox_i;

                    //$(this).append("<input type='text' class='dragme' name='textbox' id="+id+" value='' >");
                    $(this).append("<div id="+id+">"+textbox_i+" &nbsp<img id="+id+"img  height='30' width='130' class='dragme' src=assets/images/name_label.png >  <label class="+id+"ele>&nbspName</label><input type='text'  name='textbox' class="+id+"ele id="+id+"ele value='' ></div>");


                    break;
                }
                case "button":
                {
                    if(button_i>=1)
                    {
                        
                        break;
                    }
                    button_i++;
                    var id=$(ui.draggable).attr("id")+"_"+button_i;
                    
                  //  $(this).append("<input type='button' class='button dragme' name='button' id="+id+" value='submit' >");
                  $(this).append("<div id="+id+">"+button_i+" &nbsp<img id="+id+"img  height='50' width='130' class='dragme' src=assets/images/button.png ><input type='button'  name='button' class='"+id+"ele button' id="+id+"ele value='submit' ></div>");
                  
                     break;

                }
                case "date":
                {
                   
                  date_i++; 
                    var id=$(ui.draggable).attr("id")+"_"+date_i;
                    
                  //  $(this).append("<input type='button' class='button dragme' name='button' id="+id+" value='submit' >");
                  $(this).append("<div id="+id+">"+date_i+" &nbsp<img id="+id+"img  height='35' class='dragme' src=assets/images/date_label.png >  <label class="+id+"ele>Date</label>&nbsp<input type='date'  name='date' id="+id+"ele class="+id+"ele  ></div>");
                  
                     break;

                }
                case "fileupload":
                {
                    fileupload_i++; 
                    var id=$(ui.draggable).attr("id")+"_"+fileupload_i;
                    
                  //  $(this).append("<input type='button' class='button dragme' name='button' id="+id+" value='submit' >");
                  $(this).append("<div id="+id+">"+fileupload_i+" &nbsp<img id="+id+"img  height='35' class='dragme' src=assets/images/file_upload.png >  <label class="+id+"ele>File Upload</label>&nbsp <input type='file'  name='file' id="+id+"ele class="+id+"ele  ></div>");
                  
                     break;                    
                }
            }
            $("."+id+"ele").hide();
            enableDrag(id,1);
            imgIds[button_i+textbox_i+date_i+fileupload_i]=id;



        }
    });

    function enableDrag(id,toggle)
    {
        if(toggle)
         $("#"+id).draggable();
        else
         $("#"+id).draggable('disable')
    }

    $("#submit").click(function() {
        //$( "img" ).remove( "#container" )
        //console.log(imgIds);
        var elem;
        for (var i = 1; i <imgIds.length; i++) 
        {
            enableDrag(imgIds[i],0);
            $("."+imgIds[i]+"ele").show();
            //console.log(document.getElementById(imgIds[i]+"ele"));
             elem = document.getElementById(imgIds[i]+"img");
             //console.log(elem);
             elem.parentNode.removeChild(elem);
        }
        var formName = prompt("Enter form name:", "");
        if (formName == null || formName == "") 
        {
            formName = "";
        } 

        var form = { "forms" :$("#Container").html(),
                        "name":formName,
                        "elements":imgIds
        };
            $.ajax({
            type: 'POST',
            url: window.location.origin+'/api/createForm',
            data: JSON.stringify(form),
            contentType: "application/json",
            complete:function(msgresponse)
            {
                //console.log(msgresponse);
            $('#msgdiv').removeClass();
                if(msgresponse.responseJSON.response=='success')
                {
                    $('#msg').html("Form created successfully");
                    $('#msgdiv').addClass('success');
                    $('#msgdiv').show().fadeIn('slow');

                    $('#msgdiv').delay(3000).fadeOut('slow');

                    $("#Container").html("<p id='clearMe'>Drop here</p>");
                    textbox_i=0;
                    button_i=0;
                    date_i=0;
                    fileupload_i=0;
                    imgIds=[];

                }
                else
                {
                    
                    $('#msgdiv').addClass('error');

                     $('#msg').html(msgresponse.message);

                    $('#msgdiv').show().fadeIn('slow');

                    $('#msgdiv').delay(3000).fadeOut('slow');
                }

            },
            error:function()
            {
               
            }

                
        });
    });
});
</script>


