$(document).ready(function () {
    // On dbl-click create prompt asking if user wants to log this habit. If yes, hit endpoint with proper habit_id and status values
    $('section.habits ul li').on('click', function () {
        
    })
    // On shift+click hide from view
    $('section.habits ul li').click(function (e) {
        if (e.altKey) {
            $(this).css('display', 'none');
        }
    });
});