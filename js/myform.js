/**/
// Can also be used with $(document).ready()

/*no conflict the jquery parameter*/
var $ = jQuery.noConflict();

$(document).ready(function() {
       
    jQuery('input[type="checkbox"]').on('change', function(e) { 
       post_types = []; 
		$('input[type="checkbox"]').each(function(){
			if ($(this).prop('checked')==true){ 
				post_types.push($(this).val());
			}
		});	
		
		jQuery.ajax({
            url : alert_ajax.ajax_url,
            type : 'post',
			dataType:"html",
            data : {
                action : 'getajaxdata',
                post_type : post_types
            },
            success : function( response ) {
				$('#allpost').html('');
                $('#allpost').html(response);
            }
        });
		
	});
    
                    
});     
