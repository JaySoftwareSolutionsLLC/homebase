$(document).ready(function() {
    // Function that returns a greeting based on the current time by calling the getCurrentDate function
    function generateGreeting() {
        "use strict";
        var earlyMorningGreeting = "My...you're up early today Brett.";
        var morningGreeting = "Good morning Brett.";
        var afternoonGreeting = "Good afternoon Brett.";
        var eveningGreeting = "Good evening Brett.";
        var veryLateGreeting = "It is getting quite late Brett. You should get some sleep!";
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
    speak(generateGreeting());
});