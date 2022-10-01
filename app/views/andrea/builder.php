<div class="main-slider_content">
    <h1 class="main-slider_title font-weight-bold text-shadow ">Ready to create a magical personalised children's book?</h1>
    <h2 class="main-slider_smalltitle font-weight-bold text-shadow">In just a few clicks you'll have a gift they'll treasure forever</h2>
    <div class="home_pic_container" id="create_loading_on" style="display: none">
        <div class="col-md-6 col-md-offset-3 clearfix">
            <img src="public/assets/andrea/img/loading.png" class="blog-preview-small_img">
            <h4 class="main-slider_smalltitle font-weight-bold text-shadow">Your tale is under construction!</h4>
        </div>

    </div>
    <div class="home_pic_container" id="create_loading_off">

        <div class="childs_home">
            <h3 id="choose_kids_h3" class="main-slider_smalltitle text-shadow">Choose kid's character</h3>
            <div class="choose-kids" who="bmr" gender="m"><img src="public/assets/andrea/img/passport-photo/bmr.png">
                <div class="check-childs"></div>
            </div>
            <div class="choose-kids" who="bmn" gender="m"><img src="public/assets/andrea/img/passport-photo/bmn.png"></div>
            <div class="choose-kids" who="bmm" gender="m"><img src="public/assets/andrea/img/passport-photo/bmm.png"></div>
            <div class="choose-kids" who="bmb" gender="m"><img src="public/assets/andrea/img/passport-photo/bmb.png"></div>
            <div class="choose-kids" who="bfr" gender="f"><img src="public/assets/andrea/img/passport-photo/bfr.png"></div>
            <div class="choose-kids" who="bfn" gender="f"><img src="public/assets/andrea/img/passport-photo/bfn.png"></div>
            <div class="choose-kids" who="bfm" gender="f"><img src="public/assets/andrea/img/passport-photo/bfm.png"></div>
            <div class="choose-kids" who="bfb" gender="f"><img src="public/assets/andrea/img/passport-photo/bfb.png"></div>
        </div>

        <div class="parents_home">
            <h3 id="choose_parent_h3" class="main-slider_smalltitle text-shadow">Choose parent's character</h3>
            <div class="choose-parents" who="gmr" gender="m"><img src="public/assets/andrea/img/passport-photo/gmr.png"></div>
            <div class="choose-parents" who="gmn" gender="m"><img src="public/assets/andrea/img/passport-photo/gmn.png"></div>
            <div class="choose-parents" who="gmm" gender="m"><img src="public/assets/andrea/img/passport-photo/gmm.png"></div>
            <div class="choose-parents" who="gmb" gender="m"><img src="public/assets/andrea/img/passport-photo/gmb.png"></div>
            <div class="choose-parents" who="gfr" gender="f"><img src="public/assets/andrea/img/passport-photo/gfr.png"></div>
            <div class="choose-parents" who="gfb" gender="f"><img src="public/assets/andrea/img/passport-photo/gfb.png"></div>
            <div class="choose-parents" who="gfn" gender="f"><img src="public/assets/andrea/img/passport-photo/gfn.png"></div>
            <div class="choose-parents" who="gfm" gender="f"><img src="public/assets/andrea/img/passport-photo/gfm.png"></div>
        </div>
        <div class="ConfiguratorBox clearfix">
            <div class="input_cho">
                <input name="chidsname" id="chidsname" type="text" value="" placeholder="Child's name">
            </div>
            <div class="input_cho">
                <input name="parentsname" id="parentsname" type="text" placeholder="Parents's name" value="">
            </div>
            <div class="input_cho">
                <select name="language">
                    <option value="en">English</option>
                    <option value="de">Deutsch</option>
                    <option value="fr">Français</option>
                    <option value="it">Italiano</option>
                    <option value="es">Español</option>
                </select>
            </div>
            <div class="input_cho">
                <select name="story">
                    <option value="gu">Taste</option>
                    <option value="vi" disabled="disabled">Sight</option>
                    <option value="ol" disabled="disabled">Smell</option>
                    <option value="it" disabled="disabled">Touch</option>
                    <option value="ud" disabled="disabled">Hearing</option>
                </select>
            </div>

            <div id="create_book" class="button_cho" style="background:#CCC">Create Your Book</div>

            <script>
                $(document).ready(function() {
                    $("#chidsname, #parentsname").bind('keyup mouseup', function() {
                        $.fn.checkform();
                    });
                    $(".childs, .parents").mouseleave(function() {
                        $(this).hide();
                        $('.c').fadeTo(1, 1);
                    });
                });


                $('#create_book').click(function() {
                    $('#name_p').val($('#parentsname').val());
                    $('#name_b').val($('#chidsname').val());
                    $('#tale_language').val($('select[name="language"]').val());
                    $('#tale_story').val($('select[name="story"]').val());

                    if ($('#chidsname').val() == "") {
                        $("#chidsname").css('border', '1px solid red');
                    } else if ($('#parentsname').val() == "") {
                        $("#parentsname").css('border', '1px solid red');
                    } else if ($('#characterkids').val() == "") {
                        $("#choose_kids_h3").addClass("error-form");
                    } else if ($('#characterparents').val() == "") {
                        $("#choose_parent_h3").addClass("error-form");
                    } else {
                        $('#create_loading_off').hide();
                        $('#create_loading_on').show();

                        $("#create_tale").submit();
                    }

                });
            </script>
        </div>
    </div>

</div>