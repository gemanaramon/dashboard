$(document).ready(function(){

 validateTime();
   function validateTime(){

    jQuery.ajax({
        url:'query/administrative.php?validateTime', 
        method: 'get',
        data:{},
        cache: false,
        dataType: 'json',
        error: function(xhr, status, error) {
        alert(xhr.responseText);
        },
        success: function(res){
            var resultData=res.errcode;
            if(resultData=='1'){
                // $("#alassave").hide(); 
            }
        }
    });
   }


});