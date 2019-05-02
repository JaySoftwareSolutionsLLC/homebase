var timerState = 'begin';
var timerInterval;
function startTimer(startingMin, displayElement, buttonElement, inputElement) {
    let newMin = startingMin;
    buttonElement.empty().html('Stop');
    displayElement.addClass('blink')
    timerInterval = setInterval(() => {
        newMin++;
        displayElement.empty().html( returnHrsMinFromMin(newMin) );
        inputElement.val(newMin).trigger('change');
    }, 60000);
    timerState = 'active';
}
function stopTimer(buttonElement) {
    clearInterval(timerInterval);
    buttonElement.empty().html('Resume');
    hideModal();
    timerState = 'begin';
}
function returnHrsMinFromMin(minutes) {
    let hrs = Math.floor(minutes / 60);
    if (hrs < 10) {
        hrs = "0" + hrs;
    }
    let remainingMin = (minutes % 60);
    if (remainingMin < 10) {
        remainingMin = "0" + remainingMin;
    }
    return `${hrs}:${remainingMin}`;
}
function generateTimerModal(triggerElement, inputElement, title) {
    triggerElement.on('click', function( event ) {
        timerState = 'begin';
        let startingMin = inputElement.val();
        let display = returnHrsMinFromMin(startingMin);
        let info = `<h2>${title}</h2>`;
        info += `<h3 class='timer-display' style='font-size: 3rem; align-self: center;'>${display}</h3>`;
        info += `<button style='width: 60%; align-self: center;'>Start</button>`;
        showModal(event, info);
        $('#modal button').on('click', function() {
            switch (timerState) {
                case 'begin':
                    startTimer(startingMin, $('#modal h3.timer-display'), $('#modal button'), inputElement);
                    break;
                case 'active':
                    stopTimer($('#modal button'));
                default:
                    break;
            }
        });
    });
}
generateTimerModal( $('span#mindfulness-min-timer'), $('input#mindfulness-minutes-input'), 'Mindfulness Minutes' );
generateTimerModal( $('span#dev-min-timer'), $('input#software-dev-minutes-input'), 'Software Dev Minutes' );
generateTimerModal( $('span#tanning-min-timer'), $('input#tanning-minutes-input'), 'Tanning Minutes' );

$('#modal').on('click', function(event) {
    event.stopPropagation(); // Prevent modal close if click is inside of modal box
});
$('body').on('click', function() {
    hideModal();
});
