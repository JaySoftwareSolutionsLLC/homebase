var speak;
$(document).ready(function() {
	"use strict";
	/* Function: when called will speak the argument via the client speaker */  
	speak = function(messageToBeSpoken) {
		var message = new window.SpeechSynthesisUtterance();
		message.default = false;
		message.text = messageToBeSpoken;
		message.lang = 'en-GB';
		message.voiceURI = 'Google UK English Female';
		message.name = 'Google UK English Female';
		message.pitch = 1.1;
		message.rate = 1;
		message.volume = 1.0;
		window.speechSynthesis.speak(message);
	};
});