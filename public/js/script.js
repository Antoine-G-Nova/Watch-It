$(window).scroll(function() {
    if ($(document).scrollTop() > 17) {
    $('.navbar').addClass('shrink');
    }
    else {
    $('.navbar').removeClass('shrink'); }
});

$('select').select2({
    theme:"classic"
});

