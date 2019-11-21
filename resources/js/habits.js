let hiddenHabits = [];
// On dbl-click create prompt asking if user wants to log this habit. If yes, hit endpoint with proper habit_id and status values
function listenForHabitDblClickEvents() {
    $('section.habits ul li').on('dblclick', function (e) {
        // Shift + Dbl click creates a 'Started' event
        let status = e.shiftKey ? 'Started' : 'Completed';
        // confirm with user
        let habitId = $(this).attr('data-id');
        let habitName = $(this).children('span').first().html();
        let verified = confirm(`Are you sure you would like to make a ${status} ${habitName} event?`);
        if (verified) {
            // update db
            $.ajax({
                type: "POST",
                url: '/homebase/resources/ajax/insert_habit_log.php',
                dataType: 'JSON',
                data: {
                    'habit_id' : habitId,
                    'status': status
                }
            }).done(function (responseJSON) {
                if (responseJSON.success) {
                    refreshHabitList();
                }
            })
        }
    })
}
function listenForHabitAltClickEvents() {
    $('section.habits ul li').on('click', function (e) {
        if (e.altKey) {
            let habitId = $(this).attr('data-id');
            hiddenHabits.push(habitId);
            $('section.habits ul li').each(function (index, element) {
                if ($(this).attr('data-id') == habitId) {
                    $(this).css('display', 'none');
                }
            });
        }
    });
}
function refreshHabitList() {
    let newHabitList = '';
    $.ajax({
        type: "GET", // POST, GET, etc.
        url: "/homebase/resources/ajax/retrieve_habit_list.php",
        data: "", // Can also be an array { val1 : val1, val2: val2 }
        // dataType: "",
        success: function (response) {
            $('section.habits main').html(response);
            $('section.habits ul li').each(function (index, element) {
                let habitId = $(this).attr('data-id');
                if (hiddenHabits.includes(habitId)) {
                    $(this).css('display', 'none');
                }
            });
            listenForHabitDblClickEvents();
            listenForHabitAltClickEvents();
            hideHiddenHabits(hiddenHabits);
        }
    })
}
function hideHiddenHabits(hiddenHabits = hiddenHabits) {
    $('section.habits ul li').each(function (index, element) {
        if (hiddenHabits.includes($(this).attr('data-id'))) {
            $(this).css('display', 'none');
        }
    });
}
listenForHabitDblClickEvents();
listenForHabitAltClickEvents();