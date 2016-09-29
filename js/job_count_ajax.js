jQuery(document).ready(function(){

var ajaxurl=ajaxobject.ajax_url;
var wjmljc_refresh_timer=ajaxobject.refresh_time;
jQuery.ajax({
              type:"POST",
              data: { 
                  action: "wjmljc_action_ajax",
              },
              dataType: 'json',
              url: ajaxurl,
              success: function(response){
                jQuery(".wjmljc_count_job").html(response['count_job']);
                jQuery(".wjmljc_count_company").html(response['count_company']);
                jQuery(".wjmljc_count_seeker").html(response['count_seeker']);
              }
            });

var call_ajax=function(){

jQuery.ajax({
              type:"POST",
              data: { 
                  action: "wjmljc_action_ajax",
              },
              dataType: 'json',
              url: ajaxurl,
              success: function(response){
                jQuery(".wjmljc_count_job").html(response['count_job']);
                jQuery(".wjmljc_count_company").html(response['count_company']);
                jQuery(".wjmljc_count_seeker").html(response['count_seeker']);
              //console.log(response.count_job);
              }
            });
}
if (jQuery("#wpjmljc_ajax_active").val() == 'wpjmljc_002') {;
          setInterval(call_ajax,wjmljc_refresh_timer);
        }
  });


  