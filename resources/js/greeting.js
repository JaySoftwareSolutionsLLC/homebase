$(document).ready(function() {
    // Function that returns a greeting based on the current time by calling the getCurrentDate function
    function generateGreeting() {
        "use strict";
        let earlyMorningGreeting = "My...you're up early today Brett.";
        let morningGreeting = "Good morning Brett.";
        let afternoonGreeting = "Good afternoon Brett.";
        let eveningGreeting = "Good evening Brett.";
        let veryLateGreeting = "It is getting quite late Brett. You should get some sleep!";
        if (getCurrentDate().getHours() >= 4 && getCurrentDate().getHours() < 6) {
            return earlyMorningGreeting;
        } else if (getCurrentDate().getHours() >= 6 && getCurrentDate().getHours() < 12) {
            return morningGreeting;
        } else if (getCurrentDate().getHours() >= 12 && getCurrentDate().getHours() < 18) {
            return afternoonGreeting;
        } else if ((getCurrentDate().getHours() >= 18 && getCurrentDate().getHours() <= 24) || (getCurrentDate().getHours() >= 0 && getCurrentDate().getHours() < 1)) {
            return eveningGreeting;
        } else {
            return veryLateGreeting;
        }
    }
    // DEPRECATED speak(generateGreeting());
});