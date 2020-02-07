let hiddenHabits = [];
// On dbl-click create prompt asking if user wants to log this habit. If yes, hit endpoint with proper habit_id and status values
function listenForHabitDblClickEvents() {
    console.log('Start: listenForHabitDblClickEvents');
    $('section.habits ul#habits-list li').on('dblclick', function (e) {
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
    console.log('Finish: listenForHabitDblClickEvents');
}
function listenForHabitAltClickEvents() {
    console.log('Start: listenForHabitAltClickEvents');
    $('section.habits ul#habits-list li').on('click', function (e) {
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
    console.log('Finish: listenForHabitAltClickEvents');
}
function refreshHabitList() {
    console.log('Start: refreshHabitList');
    let newHabitList = '';
    $.ajax({
        type: "GET",
        url: "/homebase/resources/ajax/retrieve_habit_list.php",
        data: "",
        success: function (response) {
            $('section.habits main ul#habits-list').html(response);
            $('section.habits ul li').each(function (index, element) {
                let habitId = $(this).attr('data-id');
                if (hiddenHabits.includes(habitId)) {
                    $(this).css('display', 'none');
                }
            });
            listenForHabitDblClickEvents();
            listenForHabitAltClickEvents();
            hideHiddenHabits(hiddenHabits);
            updateHabitTime();
        }
    })
    console.log('Finish: refreshHabitList');
}
function listenForHabitSelectionClickEvents() {
    console.log('Start: listenForHabitSelectionClickEvents');
    $('ul#habit-selection li').on('click', function() {
        let sel = $(this).attr('data-selection');
        switch (sel) {
            case 'all':
                $('section.habits ul#habits-list li').each(function (index, element) {
                    $(this).css('display', 'flex')
                });
                break;
                case 'started':
                    $('section.habits ul#habits-list li').each(function (index, element) {
                        if ($(this).hasClass('started')) {
                            $(this).css('display', 'flex');
                    }
                    else {
                        $(this).css('display', 'none');
                    }
                });
                break;
            case 'clear-started':
                $.ajax({
                    type: "POST", // POST, GET, etc.
                    dataType: "JSON",
                    data: {
                        date : ed
                    },
                    url: "/homebase/resources/ajax/clear-started-habits.php",
                    success: function (responseJSON) {
                        console.log(responseJSON);
                        if (responseJSON.success) {
                            refreshHabitList();
                        }
                    }
                });
                break;
            case 'incomplete':
                $('section.habits ul#habits-list li').each(function (index, element) {
                    if ($(this).hasClass('no-further-action-this-window') || $(this).hasClass('no-further-action-today')) {
                        $(this).css('display', 'none');
                    }
                    else {
                        $(this).css('display', 'flex');
                    }
                });
                break;
        
            default:
                break;
        }
        updateHabitTime();
    })
    console.log('Finish: listenForHabitSelectionClickEvents');
}
function updateHabitTime() {
            console.log('Start: updateHabitTime');
            let minutes = 0;
    $('section.habits ul#habits-list li:visible').each(function (index, element) {
        console.log($(this));
        // alert($(this));
        minutes += parseInt($(this).attr('data-minutes-to-complete'))
    })
    readable_time = convertTimeToReadable(unit = 'minutes', minutes);
    $('table#habits-summary tbody').html(`<tr><td>${readable_time}</td></tr>`)
    console.log('Finish: updateHabitTime');
}
function hideHiddenHabits(hiddenHabits) {
    console.log('Start: hideHiddenHabits');
    $('section.habits ul li').each(function (index, element) {
        if (hiddenHabits.includes($(this).attr('data-id'))) {
            $(this).css('display', 'none');
        }
    });
    console.log('Finish: hideHiddenHabits');
}
listenForHabitDblClickEvents();
listenForHabitAltClickEvents();
listenForHabitSelectionClickEvents();
updateHabitTime()
