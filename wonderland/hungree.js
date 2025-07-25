$(document).ready(function () {
	$.localScroll();
	// $(".box").hide();

	if (!Modernizr.svg) {
  		$(".logo").css("background-image", "url(logo.png)");
}

    var url=document.URL.split('#')[1];
    if(url == undefined){
        url = '';
    }

    if(url != ''){
        $('.box').show();
        $('.box').delay(4000);
        $('.box').fadeOut(2000);
    }

});

$(document).scroll(function(e){

    var twitterHeight = $('.twitter-box').height();

    var scrollAmount = $(window).scrollTop();
    var screen01height = $('#screen-02').height() - 120;
    var screen02height = $('#screen-02').height() + $('#screen-02').height() - 120;
    var screen03height = $('#screen-02').height() + $('#screen-02').height() + $('#screen-03').height() - 120;
    var screen04height = $('#screen-02').height() + $('#screen-02').height() + $('#screen-03').height() + $('#screen-04').height() - 120;
    var screen05height = $('#screen-02').height() + $('#screen-02').height() + $('#screen-03').height() + $('#screen-04').height() + $('#screen-05').height() - 120;
    var screen06height = $('#screen-02').height() + $('#screen-02').height() + $('#screen-03').height() + $('#screen-04').height() + $('#screen-05').height() + $('#screen-06').height() - 120;

    if(scrollAmount < screen01height) {
    $('.menubar').css({ 'background-color' : '#eee'});
}
    if(scrollAmount > screen01height && scrollAmount < screen03height) {
    $('.menubar').css({ 'background-color' : '#fff'});
}
  if(scrollAmount > screen02height && scrollAmount < screen03height) {
    $('.menubar').css({ 'background-color' : '#eee'});
}
  if(scrollAmount > screen03height && scrollAmount < screen04height) {
    $('.menubar').css({ 'background-color' : '#fff'});
}
  if(scrollAmount > screen04height && scrollAmount < screen05height) {
    $('.menubar').css({ 'background-color' : '#eee'});
}
  if(scrollAmount > screen05height && scrollAmount < screen06height) {
    $('.menubar').css({ 'background-color' : '#fff'});
}  
 
});