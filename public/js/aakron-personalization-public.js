(function( $ ) {
    'use strict';

    /**
     * All of the code for your public-facing JavaScript source
     * should reside in this file.
     *
     * Note: It has been assumed you will write jQuery code here, so the
     * $ function reference has been prepared for usage within the scope
     * of this function.
     *
     * This enables you to define handlers, for when the DOM is ready:
     *
     * $(function() {
     *
     * });
     *
     * When the window is loaded:
     *
     * $( window ).load(function() {
     *
     * });
     *
     * ...and/or other possibilities.
     *
     * Ideally, it is not considered best practise to attach more than a
     * single DOM-ready or window-load handler for a particular page.
     * Although scripts in the WordPress core, Plugins and Themes may be
     * practising this, we should strive to set a better example in our own work.
     */
    jQuery(document).ready(function($){
        var spinnerImage = aakron_design_public_obj.pluginsUrl + 'images/ajax_spinner.gif';
        var pluginDirUrl                 = $('#js_design_tool_div').find('#js_design_tool_site_uri').val();
        /*var siteUrl               = window.location.origin;*/
        $('body').prepend("<div id='js_spinner' style='display:none;'><img src='"+ spinnerImage +"'></div>");
        $('body').prepend("<div id='iframeHolder' style='display:none;'></div>");
        var siteUrlRef = pluginDirUrl + 'public/js/postMessageScript.js';
        console.log(siteUrlRef);
        $('body').prepend('<script type="text/javascript" src="' + siteUrlRef + '"></script>');
        $('#aakron_artwork_design').click(function(){
            $('#js_spinner').css('display','block');
            var requestUri              = $('#js_design_tool_div').find('#js_design_tool_product_uri').val();
            var userAccessToken         = $('#js_design_tool_div').find('#js_design_tool_validate_token').val();
            var toolProductSku          = $('#js_design_tool_div').find('#js_design_tool_product_sku').val();
            var toolProductVirtualId    = $('#js_design_tool_div').find('#js_design_tool_virtual_id').val();
            var queryParmeter           = requestUri + '/' + toolProductSku + '/' + toolProductVirtualId;
            console.log(js_design_tool_site_uri);
            jQuery.ajax({
                type : "post",
                url : aakron_design_public_obj.ajax_url,
                data : {
                    action: "aakron_design_tool_validate_token",
                    userAccessToken : userAccessToken,
                    toolProductSku : toolProductSku,
                    toolProductVirtualId : toolProductVirtualId
                },
                success: function(response) {
                    var resultObj = JSON.parse(response);
                    console.log(resultObj);
                    if(!$('#iframe').length) {
                        $('#iframeHolder').html('<a href="javascript:void(0)" class="iframeholder-close">x</a> <iframe id="iframe" src="' + queryParmeter +'" style="display: block;width: 95%;position: fixed;height: 100%;z-index: 1111;left: 0;right: 0;margin: 0 auto;"></iframe>');
                        $("#iframeHolder iframe").on("load", function () {
                            document.querySelector("#iframeHolder iframe").contentWindow.postMessage({token: userAccessToken, origin: window.location.origin},requestUri);
                            $('#js_spinner').css('display','none');
                            $('#iframeHolder').css('display','block');
                        });
                    }
                    else{
                        $('#js_spinner').css('display','none');
                        $('#iframeHolder').css('display','block');
                    }
                }
            });
        });
    });

    $(document).on('click','#iframeHolder a.iframeholder-close', function(){
        $(this).parent().hide();
    });
    
    $(document).on('mouseover','.single_add_to_cart_button', function(){
        var artworkUrl = $('#js_design_tool_div').find('#js_design_tool_frontend_area img').attr('src');
        var artImgOnly = $('#js_design_tool_div').find('input#js_artwork_only_image').val();

        var artImgColor = $('#js_design_tool_div').find('input#js_artwork_only_color').val();
        var artImgImprint = $('#js_design_tool_div').find('input#js_artwork_only_imprint_location').val();
        
        var userArworkImages = [artworkUrl,artImgOnly,artImgColor,artImgImprint];
        //console.log(userArworkImages);

        jQuery.ajax({
            type : "post",
            url : aakron_design_public_obj.ajax_url,
            data : {
                action: "aakron_design_tool_add_custom_data_to_cart",
                userArworkImages : userArworkImages
            },
            success: function(response) {
                //console.log(response);
            }
        });
    });
})( jQuery );