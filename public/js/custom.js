jQuery(document).ready(function($) {

    $('#code_copy').on('click', function() {

        let value = $(this).val();
        navigator.clipboard.writeText(value)
        .then(() => {
            console.log('Text copied to clipboard');
            $('<small class="refer-success-message">Code Copied Successful</small>').insertAfter('.code_copy_status');
            
            setTimeout(() => {
                $(".refer-success-message").remove();
            }, 3000);
            
            
        })
       
    });

    
});
