jQuery(document).ready(function($) {

    $(document).ready(function() {

        $('#license_form').on('submit', function(event) {
            let  licenseKey = $('#check_license_key').data('lice');
            if(licenseKey == undefined){
                licenseKey = $('#referral_license_key').val();
            } 
            var target = window.location.origin;
            event.preventDefault();
            validateLicenseKey(licenseKey, target);
        });
        
    });
    
    function validateLicenseKey(licenseKey, target) { 
        $.ajax({
            url: 'https://www.leratech.ro/wp-json/referral/v1/validate-license-key',
            type: 'GET', 
            data: {
                license_key: licenseKey,
                target: target
            },
            success: function(response) {
                if (response) {
                    updateLicenseStatus(licenseKey, response.eS, response.eP);
                } else {
                    alert(response.message);
                }
            },
            error: function(xhr, status, error) {
                console.log(xhr.responseText);
            
            }
            
        });
    }
    
    function updateLicenseStatus(licenseKey, eS, eP) {
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'update_license_status',
                licenseKey: licenseKey, 
                eS: eS,
                eP: eP
            },
            success: function(response) {
                console.log('Success:', response);
                location.reload();
            },
            error: function(xhr, status, error) {
                console.log(xhr);
            }
            
        });
    };

    $('.copy-shortcode-button').on('click', function() {
        var copyText = document.getElementById("referral_shortcode");
        copyText.select();
        document.execCommand("copy");
        jQuery(".refer-success-message").remove();

        jQuery("</br></br><large class=\'refer-success-message\' style=\'color: green;\'>Shortcode Copied Successfully!</large>")
            .insertAfter("#referral_shortcode");

        setTimeout(() => {
            jQuery(".refer-success-message").remove();
        }, 3000);
    });
    

});