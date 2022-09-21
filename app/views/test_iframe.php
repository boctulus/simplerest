<h3>iFrame test</h3>

<?php
/*
    Make iframe automatically adjust height according to the contents without using scrollbar?
    
    Note: This will not work if the iframe contains content from another domain because of the Same Origin Policy
*/

js("
function resizeIframe(obj) {
    obj.style.height = obj.contentWindow.document.documentElement.scrollHeight + 'px';
}

$(window).load(function(){
    $(document).scroll(function () {
        var scrollTop = $(window).scrollTop();
        var docHeight = $(document).height();
        var winHeight = $(window).height();
        var scrollPercent = scrollTop / (docHeight - winHeight);

        var divHeight = $('div').height(); 
        var divContentHeight = $('div')[0].scrollHeight;

        var equation = scrollPercent * (divContentHeight-divHeight);

        $('div').scrollTop(equation);

    });     
});

");

css('
.iframe_container {
    position:relative; 
    width:600px;
    height:100%;
    max-width:100%;
}

.my_iframe {
    display:block;
    width:100%;
    height:100%;
    position:absolute; top:0; left: 0;
}
');
?>

<center>
    <div class="iframe_container">
        <iframe class="my_iframe" marginwidth="0" marginheight="0" allowfullscreen frameborder="0" scrolling="no" onload="resizeIframe(this)" src="https://produzione.familyintale.com/">Your Browser Does Not Support iframes!</iframe>
    </div>
</center>
