jQuery(function($){
    $(document).ready(function(){
        $('.notice-dismiss').on('click', function(){
            let notice_id = $(this).closest('.sirv-admin-notice').attr('data-notice-id');
            if(!!notice_id){
                $.post(ajaxurl,{
                    action: 'sirv_dismiss_notice',
                    notice_id : notice_id,
                }).done(function(response){
                    //debug
                    console.log(response);

                }).fail(function(jqXHR, status, error){
                    console.error("Error during ajax request: " + error);
                });
            }
        });
    }); //domready end
});
