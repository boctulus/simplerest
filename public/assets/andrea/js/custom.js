
$('.choose-parents').click(function() {
    var who = $(this).attr('who');
    var gender = $(this).attr('gender');
    $('#characterparents').val(who);
    $('#genderparents').val(gender);
    $('.check-parents').hide();
    $(this).append('<div class="check-parents">');
    $.fn.checkform();
});

$('.choose-kids').click(function() {
    var who = $(this).attr('who');
    var gender = $(this).attr('gender');
    $('#characterkids').val(who);
    $('#genderkids').val(gender);
    $('.check-childs').hide();
    $(this).append('<div class="check-childs">');
    $.fn.checkform();
});

$(function() {
    $.fn.checkform = function () {
        if($('#chidsname').val()!="" && $('#parentsname').val()!=""  &&  $('#characterkids').val()!=""  &&  $('#characterparents').val()!="") {
            $('#create_book').css('background','#093');
            $("#chidsname").css('border', '1px solid #336699');
            $("#parentsname").css('border', '1px solid #336699');
            $("#choose_parent_h3").removeClass("error-form");
            $("#choose_kids_h3").removeClass("error-form");
        }
        else {
            $('#create_book').css('background','#CCC');
                if($('#chidsname').val()!="") {
        $("#chidsname").css('border', '1px solid #336699');
    }
    if($('#parentsname').val()!="") {
        $("#parentsname").css('border', '1px solid #336699');
    }
    if($('#characterparents').val()!="") {
        $("#choose_parent_h3").removeClass("error-form");
    }
    if($('#characterkids').val()!="") {
        $("#choose_kids_h3").removeClass("error-form");
    }

        }
    };
});


var Page = (function() {    
    var config = {
            $bookBlock : $( '#bb-bookblock' ),
            $navNext : $( '#bb-nav-next' ),
            $navPrev : $( '#bb-nav-prev' ),
            $navFirst : $( '#bb-nav-first' ),
            $navLast : $( '#bb-nav-last' )
        },
        init = function() {
            config.$bookBlock.bookblock( {
                speed : 800,
                shadowSides : 0.8,
                shadowFlip : 0.7
            } );
            initEvents();
        },
        initEvents = function() {
            
            var $slides = config.$bookBlock.children();

            // add navigation events
            config.$navNext.on( 'click touchstart', function() {
                config.$bookBlock.bookblock( 'next' );
                return false;
            } );

            config.$navPrev.on( 'click touchstart', function() {
                config.$bookBlock.bookblock( 'prev' );
                return false;
            } );

            config.$navFirst.on( 'click touchstart', function() {
                config.$bookBlock.bookblock( 'first' );
                return false;
            } );

            config.$navLast.on( 'click touchstart', function() {
                config.$bookBlock.bookblock( 'last' );
                return false;
            } );
            
            // add swipe events
            $slides.on( {
                'swipeleft' : function( event ) {
                    config.$bookBlock.bookblock( 'next' );
                    return false;
                },
                'swiperight' : function( event ) {
                    config.$bookBlock.bookblock( 'prev' );
                    return false;
                }
            } );

            // add keyboard events
            $( document ).keydown( function(e) {
                var keyCode = e.keyCode || e.which,
                    arrow = {
                        left : 37,
                        up : 38,
                        right : 39,
                        down : 40
                    };

                switch (keyCode) {
                    case arrow.left:
                        config.$bookBlock.bookblock( 'prev' );
                        break;
                    case arrow.right:
                        config.$bookBlock.bookblock( 'next' );
                        break;
                }
            } );
        };

        return { init : init };

})();
		
$( document ).ready(function() {
    $('[who=bmn]').append('<div class="check-childs">');
    $('#characterkids').val('bmn');
    $('#genderkids').val('m');
    $('[who=gfr]').append('<div class="check-parents">');
    $('#characterparents').val('gfr');
    $('#genderparents').val('f');
    $.fn.checkform();
});
      

Page.init();

function reset_book(){
    var retVal = confirm("Are you sure to delete your book?");
    if( retVal ){
        location.href = "reset-book/";
    }
    else{
        return false;
    }
}
