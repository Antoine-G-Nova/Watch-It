$(window).scroll(function() {
    if ($(document).scrollTop() > 17) {
    $('.navbar').addClass('shrink').removeClass('height');
    }
    else {
    $('.navbar').removeClass('shrink').addClass('height'); }
});


