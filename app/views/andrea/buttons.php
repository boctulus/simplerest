<section id="config_tale_row" class="background-container_or">
    <div class="container modify-box">
        <div class="modify-slider">
        <div id="config_tale" class="freemodify col-lg-4 col-md-4 col-sm-4 col-xs-12 clearfix">
            <div id="modify_panel_open" class="modify-img"><span class="icon-arrow-up customColor" aria-hidden="true"></span></div>
            <div id="modify_panel_close" class="modify-img" style="display: none"><span class="icon-arrow-down customColor" aria-hidden="true"></span></div>
            <div class="freemodify-item font-additional wow fadeInUp" data-wow-delay="0.3s"> <span class="icon-settings color-green" aria-hidden="true"></span> <img class="mini-passport" src="/public/assets/andrea/img/passport-photo/bmn.png"><img class="mini-passport" src="/public/assets/andrea/img/passport-photo/gfr.png"><img class="mini-passport" src="/public/assets/andrea/img/passport-photo/gu.png"> </div>
        </div>
        <div id="dedication" class="freemodify col-lg-4 col-md-4 col-sm-4 col-xs-12 clearfix background-container">
            <div id="dedication_open" class="modify-img" style="display: none;"><span class="icon-arrow-up customColor" aria-hidden="true"></span></div>
            <div id="dedication_close" class="modify-img" style="display: block;"><span class="icon-arrow-down customColor" aria-hidden="true"></span></div>
            <div class="freemodify-item font-additional wow fadeInUp" data-wow-delay="0.3s"><span id="ded_span" class="icon-pencil color-green" aria-hidden="true"></span>YOUR DEDICATION</div>
        </div>
        
        <div id="add_cart" class="freemodify col-lg-4 col-md-4 col-sm-4 col-xs-12 clearfix">
            <div class="freemodify-item font-additional wow fadeInUp" data-wow-delay="0.3s"> <span id="cart_span" class="icon-basket color-green" aria-hidden="true"></span>ADD TO CART € 24,90</div>
        </div>
        
        </div>
    </div>
    <script>
    $('#config_tale').click(function() {
            $("#dedication_open").show();
        $("#dedication_close").hide();
        $("#dedication").removeClass("background-container");
        $('#dedication_panel').hide();
        
        $("#modify_panel_open").toggle();
        $("#modify_panel_close").toggle();
        $("#config_tale").toggleClass("background-container");
    $('#config_tale_panel').toggle();
    });

    $('#dedication').click(function() {
            $("#modify_panel_open").show();
        $("#modify_panel_close").hide();
        $("#config_tale").removeClass("background-container");
    $('#config_tale_panel').hide();
        
        $("#dedication_open").toggle();
        $("#dedication_close").toggle();
        $("#dedication").toggleClass("background-container");
        $('#dedication_panel').toggle();
    });
    </script>

    <script>
        $('#add_cart').click(function() {
            if ($("#dedication_ok").val()==1) {
                window.location.href = '/add-to-cart/';
            } else {
                $("#modify_panel_open").show();
                $("#modify_panel_close").hide();
                $("#config_tale").removeClass("background-container");
                $('#config_tale_panel').hide();
                
                $("#dedication_open").toggle();
                $("#dedication_close").toggle();
                $("#dedication").toggleClass("background-container");
                $('#dedication_panel').toggle();
            }
        });
    </script>

</section>