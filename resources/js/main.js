// Main is used to perform functions related to entire website. Different from resources in that it is applying concepts rather than just instantiating them

var getCurrentDate;
$(document).ready(function() {
  $('#modal').on('click', function(event) {
    event.stopPropagation(); // Prevent modal close if click is inside of modal box
  });
  $('body').on('click', function() {
    hideModal();
    // If nav is showing then on body click toggle hamburger img and nav
    if ($('header div.right-mobile img').css('display') == 'none') {
      $('header div.right-mobile img, header div.right-mobile nav').toggle();
    }
  });
  $('section h2').on('click', function() {
    $(this).siblings('div.content').toggle();;
  });
  $('header div.right-mobile img').on('click', function(event) {
    event.stopPropagation(); // Prevent modal close if click is inside of modal box
    console.log("Triggered");
    $(this).toggle();
    $(this).siblings('nav').toggle();
  });
});