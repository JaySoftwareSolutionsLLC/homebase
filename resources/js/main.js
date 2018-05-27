var getCurrentDate;
$(document).ready(function() {

	getCurrentDate = function() {
    	return new Date();
	}
	
  var toDoActivities = [];

  var liftingSevenDays =      [102, 68, 100, 0, 75, 100, 82];
  var programmingSevenDays =  [90, 129, 0, 17, 88, 206, 131];
  var meditationSevenDays =   [0, 90, 0, 0, 115, 20, 90];
  var spanishSevenDays =      [96, 106, 100, 0, 103, 100, 90];

  var toDoObjects = [];
  function ToDoConstructFunction(name) {
    this.name = name;
    this.completed = false;
  }
  function createToDoObjects() {
    for (var i = 0; i < toDoActivities.length; i++) {
      toDoObjects.push(new ToDoConstructFunction(toDoActivities[i]));
    }
  }
  function generateToDos() {
    weeklyToDoPush();
    createToDoObjects();
    for (var i = 0; i < toDoActivities.length; i++) {
      createToDoElement(toDoActivities[i], i);
      createToDoFunctionality(i);
    }
  }
  function createToDoElement(text, positionInQueue) {
    var textNode = document.createTextNode(text);
    var h3Element = document.createElement("h3");
    h3Element.appendChild(textNode);
    var liElement = document.createElement("li");
    liElement.appendChild(h3Element);
    var liID = "to-do-item-" + positionInQueue;
    liElement.setAttribute("id", liID);
    var olElement = document.getElementById("to-do-list");
    olElement.appendChild(liElement);
  }
  function createToDoFunctionality(positionInQueue) {
    var liID = "to-do-item-" + positionInQueue;
    document.getElementById(liID).onclick = function() {
      document.getElementById(liID).style.textDecoration = "line-through";
      document.getElementById(liID).style.opacity = "0.5";
      toDoObjects[positionInQueue].completed = true;
      var allToDosComplete = true;
      for(var i = 0; i < toDoObjects.length; i++) {
        if (toDoObjects[i].completed === false) {
          allToDosComplete = false;
        }
      }
      if (allToDosComplete) {
        speak("Congratulations Brett, you completed your to do list for today.");
        document.getElementById("to-do-div").style.display = "none";
      }
    }
  }
  generateToDos();
  function weeklyToDoPush() {
    var today = new Date().getDay();
    if (today === 0) {
      toDoActivities.push("Do Laundry");
    }
    if (today === 3) {
      toDoActivities.push("Take Out Garbage");
    }
  }

  var activities = [];
  var todayShift;

  function refresh(timesPerSecond) {
    setInterval(function() {
      location.reload();
    }, 1000 / timesPerSecond);
  }

  function displayQueue() {
    document.getElementById("queue").style.display = "inline-block";
  }
  function hideWorkSchedule() {
    document.getElementById("work-schedule").style.display = "none";
  }
  function swapQueueForWork() {
    hideWorkSchedule();
    displayQueue();
    setTargetMinutes();
    speakTargetMinutes();
    checkForCompletedTargets();
    displayLogASession();
    logASessionFunctionality();
  }

  generateActivities();

  var AMButton = document.getElementById("work-schedule-am");
  AMButton.onclick = function() {
    todayShift = "AM";
    swapQueueForWork();
  };
  var PMButton = document.getElementById("work-schedule-pm");
  PMButton.onclick = function() {
    todayShift = "PM";
    swapQueueForWork();
  };
  var DblButton = document.getElementById("work-schedule-dbl");
  DblButton.onclick = function() {
    todayShift = "Dbl";
    swapQueueForWork();
  };
  var NoneButton = document.getElementById("work-schedule-none");
  NoneButton.onclick = function() {
    todayShift = "None";
    swapQueueForWork();
  }; //Could condense the above?!

  function displayActivity(activityObject) {
    var queueDiv = document.getElementById("queue");
    var queueH2 = document.getElementById("queueH2");
    var activityElement = document.createElement("div");
    var descriptionIconElement = document.createElement("p");
    descriptionIconElement.setAttribute("class", "description-icon");
    descriptionIconElement.setAttribute("id", (activityObject.id + "-description-icon"));
    var descriptionIconText = document.createTextNode("i");
    descriptionIconElement.appendChild(descriptionIconText);
    activityElement.appendChild(descriptionIconElement);
    var logASessionElement = document.getElementById("log-a-session");
    activityElement.setAttribute("class", "queue-block");
    activityElement.setAttribute("id", activityObject.id);
    queueDiv.insertBefore(activityElement, logASessionElement);
    activityObject.location = activityElement;
  }
  for (var i = 0; i < activities.length; i++) {
    displayActivity(activities[i]);
    activityDescriptionFunctionality(activities[i]);
  }

  var todayTargetMinutes = [];
  function setTargetMinutes() {
    switch (todayShift) {
    case "AM" :
      todayTargetMinutes = [45, 150, 20, 30, 30];
      break;
    case "PM" :
      todayTargetMinutes = [45, 120, 20, 30, 20];
      break;
    case "Dbl" :
      todayTargetMinutes = [0, 15, 15, 0, 0];
      break;
    case "None" :
      todayTargetMinutes = [45, 180, 30, 45, 30];
      break;
    }
    for (var i = 0; i < activities.length; i++) {
      activities[i].target = todayTargetMinutes[i];
    }
  }
  function determineTotalMinutes() {
    return activities[0].target + activities[1].target + activities[2].target + activities[3].target;
  }
  function speakTargetMinutes() {
    speak("Today's targets are...")
    for (var i = 0; i < activities.length; i++) {
      if (activities[i].target !== 0) {
        if (i !== (activities.length - 1)) {
          speak(activities[i].name + " for " + activities[i].target + " minutes.");
        }
        else {
          speak("and, " + activities[i].name + " for " + activities[i].target + " minutes.");
        }
      }
    }
    speak("For a grand total of " + determineTotalMinutes() + " minutes.");
  }
  function checkForCompletedTargets() {
    for (var i = 0; i < activities.length; i++) {
      if (activities[i].target <= activities[i].dailyMinutes) {
        changeOpacity(activities[i].location, "1.0");
      }
    }
  }

  function ActivityConstructFunction(name, id, generalGoal, description, sevenDays) {
    this.name = name;
    this.id = id;
    this.generalGoal = generalGoal;
    this.dailyMinutes = 0;
    this.isActive = false;
    this.firstAlarm = true;
    this.description = description;
    this.descriptionIsGenerated = false;
    this.sevenDays = [];
  }
  function generateActivities() {
    var descriptions = [
      "Start this timer after getting dressed for lifting and generating the workout but before adjusting weights.",
      "Start this timer before beginning any programming related work, such as: brainstorming, creating wire-frames, coding, reading new programming material or studying programming concept flash-cards.",
      "Start this timer directly before beginning any meditative activity, such as: a headSpace session, a 'blank-mind' meditation or a 'focused' meditation.",
      "Starting this timer will launch Duolingo at the practice screen, but any form of Spanish practice counts, such as: reading a Spanish book, listening to a Spanish podcast or watching a Spanish show."
    ];
    var lifting = activities.push(new ActivityConstructFunction("Lifting", "queue-block-workout", "4 hours / week.", descriptions[0]));
    var programming = activities.push(new ActivityConstructFunction("Programming", "queue-block-programming", "14 hours / week.", descriptions[1]));
    var meditation = activities.push(new ActivityConstructFunction("Meditation", "queue-block-meditation", "20 minutes / day.", descriptions[2]));
    var spanish = activities.push(new ActivityConstructFunction("Spanish", "queue-block-spanish", "3 hours / week.", descriptions[3]));
    pushSevenDays(liftingSevenDays, activities[0]);
    pushSevenDays(programmingSevenDays, activities[1]);
    pushSevenDays(meditationSevenDays, activities[2]);
    pushSevenDays(spanishSevenDays, activities[3]);
    // var guitar = activities.push(new ActivityConstructFunction("Guitar", "queue-block-guitar", "2 hours / week."));
  }
  function pushSevenDays(housingArray, activityObject) {
    activityObject.sevenDays = housingArray;
  }
  function showActivityDescription(text, activityObject) {
    var parElement = document.createElement("p");
    parElement.setAttribute("class", "description");
    parElement.setAttribute("id", (activityObject.id + "-description"));
    var parElementText = document.createTextNode(text);
    parElement.appendChild(parElementText);
    document.getElementById(activityObject.id).appendChild(parElement);
  }
  function activityDescriptionFunctionality(activityObject) {
    document.getElementById(activityObject.id + "-description-icon").onmouseover = function() {
      if (activityObject.descriptionIsGenerated === false) {
        showActivityDescription(activityObject.description, activityObject);
        activityObject.descriptionIsGenerated = true;
      }
      else if (activityObject.descriptionIsGenerated) {
        changeDisplay(document.getElementById(activityObject.id + "-description"), "block");
      }
    }
    document.getElementById(activityObject.id + "-description-icon").onmouseout = function() {
      changeDisplay(document.getElementById(activityObject.id + "-description"), "none");
    }
  }

  function noOtherClassesAreActive() {
    for (var i = 0; i < activities.length; i++) {
      if (activities[i].isActive) {
        speak("Brett, aren't you forgetting to end your current " + activities[i].name + " session?")
        return false;
      }
    }
    return true;
  }
  var timerStartTime;
  function startTimer() {
    timerStartTime = new Date().getTime();
  }
  function stopTimer(activityObject) {
    var minSpentOnActivity = Math.floor((new Date().getTime() - timerStartTime) / (1000 * 60));
    activityObject.dailyMinutes += minSpentOnActivity;
    speak("stopping " + activityObject.name + " timer. Session length was " + minSpentOnActivity + " minutes.");
  }
  function returnTimeLeft(activityObject) {
    return (activityObject.target - activityObject.dailyMinutes);
  }

  function changeOpacity(location, newOpacity) {
    location.style.opacity = newOpacity;
  }
  function changeClass(location, newClass) {
    location.className = newClass;
  }
  function changeDisplay(location, displaySetting) {
    location.style.display = displaySetting;
  }
  function toggleActivity(activityObject) {
    switch (activityObject.isActive) {
      case false:
      if (noOtherClassesAreActive()) {
        startTimer();
        speak("Initiating timer for " + activityObject.name + ".");
        if (activityObject.dailyMinutes < activityObject.target) {
          speak("Target will be met in " + returnTimeLeft(activityObject) + " minutes.");
        }
        activityObject.isActive = true;
        changeClass(activityObject.location, "queue-block active");
      }
      break;
      case true:
      stopTimer(activityObject);
      activityObject.isActive = false;
      changeClass(activityObject.location, "queue-block");
      break;
    }
  }

  function timerGreaterThanTarget() {
    for (var i = 0; i < activities.length; i++) {
      if (activities[i].isActive) {
        if ((Math.floor((new Date().getTime() - timerStartTime) / (1000 * 60)) + activities[i].dailyMinutes) >= activities[i].target && activities[i].firstAlarm) {
          speak("Congratulations! You have reached today's target for " + activities[i].name + ".");
          activities[i].firstAlarm = false;
          changeOpacity(activities[i].location, "1.0");
        }
      }
    }
    // console.log("hello");
  }
  function createAppendElement(elementType, text, locationToAppend) {
    var element = document.createElement(elementType);
    var elementText = document.createTextNode(text);
    element.appendChild(elementText);
    locationToAppend.appendChild(element);
  }
  function createAppendOption(text, locationToAppend, value) {
    var element = document.createElement("option");
    element.setAttribute("value", value);
    var elementText = document.createTextNode(text);
    element.appendChild(elementText);
    locationToAppend.appendChild(element);
  }

  let intervalID = setInterval(function() {
    timerGreaterThanTarget();
  }, 10000);

  activities[0].location.onclick = function() {
    toggleActivity(activities[0]);
  }
  activities[1].location.onclick = function() {
    toggleActivity(activities[1]);
  }
  activities[2].location.onclick = function() {
    toggleActivity(activities[2]);
  }
  activities[3].location.onclick = function() {
    toggleActivity(activities[3]);
    if (activities[3].isActive) {
      window.open('https://www.duolingo.com/practice', '_blank');
    }
  }
  // activities[4].location.onclick = function() {
  //     toggleActivity(activities[4]);
  // }

  var sessionLength = 0;
  function displayLogASession() {
    var divElement = document.getElementById("log-a-session");
    createAppendElement("h2", "Log a session", divElement);
    var formElement = document.createElement("form");
    var selectElement = document.createElement("select");
    selectElement.setAttribute("id", "manual-log-selection");
    for (var i = 0; i < activities.length; i++) {
      createAppendOption(activities[i].name, selectElement, activities[i].name);
    }
    formElement.appendChild(selectElement);
    divElement.appendChild(formElement);
    var inputElement = document.createElement("input");
    inputElement.setAttribute("id", "manual-log-input");
    inputElement.setAttribute("type", "text");
    inputElement.setAttribute("placeholder", "min");
    inputElement.setAttribute("autocomplete", "off");
    formElement.appendChild(inputElement);
    var buttonElement = document.createElement("div");
    buttonElement.setAttribute("id", "manual-log-button");
    var buttonText = document.createTextNode("Submit");
    buttonElement.appendChild(buttonText);
    formElement.appendChild(buttonElement);
    selectElement.value = "";
  }
  function logASessionFunctionality() {
    var buttonElement = document.getElementById("manual-log-button");
    buttonElement.onclick = function() {
      var inputElement = document.getElementById("manual-log-input");
      var selectElement = document.getElementById("manual-log-selection");
      console.log(selectElement.value);
      console.log(inputElement.value);
      for (var i = 0; i < activities.length; i++) {
        if (selectElement.value === activities[i].name) {
          activities[i].dailyMinutes += Number(inputElement.value);
          checkForCompletedTargets();
          eraseValueOfElement("manual-log-input");
          eraseValueOfElement("manual-log-selection");
        }
      }
    }
  }
  function eraseValueOfElement(id) {
    document.getElementById(id).value = "";
  }
  function displayStats(activityObject) {
    var statsElement = document.getElementById("stats");
    createAppendElement("h2", activityObject.name, statsElement);
    createAppendElement("p", ("General Goal: " + activityObject.generalGoal), statsElement);
    if (activityObject.isActive) {
      sessionLength = Math.floor((new Date().getTime() - timerStartTime) / (60 * 1000));
      // console.log(sessionLength);
    }
    var progress = Math.floor((100*(activityObject.dailyMinutes + sessionLength)/activityObject.target));
    if (progress >= 0 && progress <= 100000) {}
    else {progress = 100;}
    createAppendElement("p", ("Today's Time: " + (activityObject.dailyMinutes + sessionLength) + " minutes"), statsElement);
    createAppendElement("p", ("Today's Target: " + activityObject.target + " minutes"), statsElement);
    createProgressBar(activityObject, statsElement);
    createAppendElement("p", "Cumulative Hours: ", statsElement);
    createAppendElement("p", "Cumulative % Targets Hit: ", statsElement);
    createAppend7DayCanvas(activityObject, statsElement);
    sessionLength = 0;
    // changeDisplay(document.getElementById("log-a-session"), "none");
  }
  function createProgressBar(activityObject, location) {
    var progress = Math.floor((100*(activityObject.dailyMinutes + sessionLength)/activityObject.target));
    if (progress >= 0 && progress <= 100000) {}
    else {progress = 100;}
    var progressBarElement = document.createElement("div");
    progressBarElement.setAttribute("class", "progress-bar");
    var progressDisplayElement = document.createElement("p");
    var progressDisplayText = document.createTextNode(progress + "%");
    progressDisplayElement.appendChild(progressDisplayText);
    progressBarElement.appendChild(progressDisplayElement);
    var progressElement = document.createElement("div");
    progressElement.setAttribute("class", "progress");
    if (progress === 0) {
      progressElement.style.width = "0%";
    }
    else if (progress <= 10) {
      progressElement.style.width = "10%";
    }
    else if (progress <= 100) {
      progressElement.style.width = progress + "%";
    }
    else {
      progressElement.style.width = "100%";
    }
    if (progress >= 100) {
      progressElement.style.background = "hsla(120, 100%, 60%, 0.7)";
    }
    progressBarElement.appendChild(progressElement);
    location.appendChild(progressBarElement);
    // var progress = document.createElement("div");
  }
  function createAppend7DayCanvas(activityObject, location) {
    var percentTargetHit = activityObject.sevenDays;
    var currDay = new Date().getDay();
    var displayDays = [];
    for (var i = 0; i < 7; i++) {
      displayDays.push(convertNumberToDayOfWeek(currDay));
      currDay++;
      if(currDay >= 7) {
        currDay -= 7;
      }
    }
    var targetYValues = [];
    var targetXValues = [];
    var canvasWidth = 0;
    var canvasHeight = 0;
    // console.log(window.innerWidth);
    if (window.innerWidth > 1225) {
      canvasWidth = 400;
      canvasHeight = 200;
    }
    else if (window.innerWidth > 900) {
      canvasWidth = 300;
      canvasHeight = 150;
    }
    else if (window.innerWidth >= 760) {
      canvasWidth = 240;
      canvasHeight = 120;
    }
    var canvasElement = createHiDefCanvas(canvasWidth, canvasHeight);
    location.appendChild(canvasElement);
    var canvasWidth = canvasElement.width;
    var canvasHeight = canvasElement.height;
    var goalHeight = canvasHeight / 2;
    var displayStartx = 35;
    var displayEndx = canvasWidth - 10;
    var displayWidth = displayEndx - displayStartx;
    var displayStarty = 20;
    var displayEndy = canvasHeight - 20;
    var displayHeight = displayEndy - displayStarty;
    var displayMargin = 20;
    var trueDisplayWidth = displayWidth - (2 * displayMargin);
    console.log(displayWidth);
    function pushValuesIntoXandYArrays() {
      for (var i = 0; i < percentTargetHit.length; i++) {
        targetYValues.push(Math.round(displayEndy - ((displayEndy - goalHeight) * 0.01 * percentTargetHit[i])));
        if (targetYValues[i] < (displayStarty + 5)) {
          targetYValues[i] = displayStarty + 5;
        }
        targetXValues.push(Math.round(displayStartx + displayMargin + (i * trueDisplayWidth / 6)));
      }
    }
    pushValuesIntoXandYArrays();
    var ctx = canvasElement.getContext('2d');
    function display7DayTitle() {
      ctx.font = '12px Orbitron';
      ctx.lineWidth = 1;
      ctx.fillStyle = 'hsla(190, 100%, 100%, 1)';
      ctx.textAlign = "center";
      ctx.fillText("Past 7 Days", (canvasWidth / 2), displayStarty - 5);
    }
    display7DayTitle();
    // console.log(displayWidth);
    function displayGraphArea() {
      ctx.fillStyle = 'hsla(0, 0%, 100%, 0.2)';
      ctx.beginPath();
      ctx.moveTo(displayStartx, displayStarty);
      ctx.lineTo(displayEndx, displayStarty);
      ctx.lineTo(displayEndx, displayEndy);
      ctx.lineTo(displayStartx, displayEndy);
      ctx.closePath();
      ctx.fill();
    }
    displayGraphArea();
    function displayYAxisValues() {
      ctx.font = '8px Orbitron';
      ctx.strokeStyle = 'hsla(190, 100%, 100%, 1)';
      ctx.fillStyle = 'hsla(190, 100%, 100%, 1)';
      ctx.textAlign = 'end';
      ctx.textBaseline = "middle";
      ctx.fillText("0%", displayStartx - 5, displayEndy);
      ctx.fillText("50%", displayStartx - 5, ((displayEndy + goalHeight)/2));
      ctx.fillText("100%", displayStartx - 5, goalHeight);
      ctx.textBaseline = "middle";
      ctx.fillText("150%", displayStartx - 5, displayEndy - ((displayEndy - goalHeight) * 1.5));
      ctx.textBaseline = "top";
      ctx.fillText("200%", displayStartx - 5, displayStarty);
      createYAxisTick(displayEndy, 3);
      createYAxisTick(((displayEndy + goalHeight)/2), 3);
      createYAxisTick(goalHeight, 3);
      createYAxisTick(displayEndy - ((displayEndy - goalHeight) * 1.5), 3);
      createYAxisTick(displayStarty + 5, 3);
    }
    function createYAxisTick(yValue, tickSize) {
      ctx.beginPath();
      ctx.moveTo(displayStartx - tickSize + 1, yValue);
      ctx.lineTo(displayStartx + tickSize + 1, yValue);
      ctx.closePath();
      ctx.stroke();
    }
    displayYAxisValues();
    function displayDayNames() {
      ctx.font = '8px Orbitron';
      ctx.textAlign = 'center';
      ctx.textBaseline = 'bottom';
      ctx.fillStyle = 'hsla(190, 100%, 100%, 1)';
      for (var i = 0; i < displayDays.length; i++) {
        // if (i === 0) {
        //   ctx.textAlign = "start";
        // }
        // else if (i === (displayDays.length - 1)) {
        //   ctx.textAlign = "end";
        // }
        // else {
        //   ctx.textAlign = "center";
        // }
        ctx.fillText(displayDays[i], targetXValues[i], displayEndy + 15);
      }
    }
    displayDayNames();
    function draw7DayGoal(sizeOfRange) {
      for (var i = sizeOfRange; i >= 0; i--) {
        ctx.strokeStyle = 'hsla(120, 100%, 50%, ' + (i / (sizeOfRange * 3)) + ')';
        // ctx.beginPath();
        // ctx.moveTo(displayStartx, goalHeight - sizeOfRange + i - 1);
        // ctx.lineTo(displayEndx, goalHeight - sizeOfRange + i - 1);
        // ctx.closePath();
        // ctx.stroke();
        ctx.beginPath();
        ctx.moveTo(displayStartx, goalHeight + sizeOfRange - i);
        ctx.lineTo(displayEndx, goalHeight + sizeOfRange - i);
        ctx.closePath();
        ctx.stroke();
      }
      for (var i = sizeOfRange * 1.5; i >= 0; i--) {
        ctx.strokeStyle = 'hsla(120, 100%, 50%, ' + (i / (sizeOfRange * 4.5)) + ')';
        ctx.beginPath();
        ctx.moveTo(displayStartx, goalHeight - (sizeOfRange * 1.5) + i - 1);
        ctx.lineTo(displayEndx, goalHeight - (sizeOfRange * 1.5) + i - 1);
        ctx.closePath();
        ctx.stroke();
      }
    }
    draw7DayGoal(20);
    function draw7DayLines() {
      ctx.strokeStyle = 'hsla(0, 0%, 100%, 0.2)';
      for (var i = 1; i < targetYValues.length; i++) {
        ctx.beginPath();
        ctx.moveTo(targetXValues[i - 1], targetYValues[i - 1]);
        ctx.lineTo(targetXValues[i], targetYValues[i]);
        ctx.closePath();
        ctx.stroke();
      }
    }
    draw7DayLines();
    function draw7DayPoints(size) {
      // ctx.fillStyle = 'hsla(0, 0%, 0%, 1)';
      ctx.fillStyle = 'hsla(190, 100%, 50%, 1)';
      for (var i = 0; i < targetYValues.length; i++) {
        ctx.beginPath();
        ctx.arc(targetXValues[i], targetYValues[i], size, 0, 2*Math.PI);
        ctx.fill();
        // ctx.stroke();
      }
    }
    draw7DayPoints(2);
    // for (var i = 0; i < targetYValues; i++) {
    //   ctx.beginPath();
    //   ctx.arc(targetXValues[i], targetYValues[i], 3, 0, 5);
    //   ctx.fill();
    //   ctx.closePath();
    // }
  }
  function convertNumberToDayOfWeek(number) {
    switch(number) {
      case 0:
      return "Sun";
      break;
      case 1:
      return "Mon";
      break;
      case 2:
      return "Tue";
      break;
      case 3:
      return "Wed";
      break;
      case 4:
      return "Thu";
      break;
      case 5:
      return "Fri";
      break;
      case 6:
      return "Sat";
      break;
      default:
      return "???";
      break;
    }
  }
  function createHiDefCanvas(w, h) {
    var PIXEL_RATIO = (function () {
      var ctx = document.createElement("canvas").getContext("2d"),
      dpr = window.devicePixelRatio || 1,
      bsr = ctx.webkitBackingStorePixelRatio ||
      ctx.mozBackingStorePixelRatio ||
      ctx.msBackingStorePixelRatio ||
      ctx.oBackingStorePixelRatio ||
      ctx.backingStorePixelRatio || 1;

      return dpr / bsr;
    })();
    createHiDPICanvas = function(w, h, ratio) {
      if (!ratio) { ratio = PIXEL_RATIO; }
      var can = document.createElement("canvas");
      can.width = w * ratio;
      can.height = h * ratio;
      can.style.width = w + "px";
      can.style.height = h + "px";
      can.getContext("2d").setTransform(ratio, 0, 0, ratio, 0, 0);
      return can;
    }
    //Create canvas with the device resolution.
    return createHiDPICanvas(w, h);
  }
  function hideStats() {
    var statsElement = document.getElementById("stats");
    statsElement.innerHTML = "";
    // changeDisplay(document.getElementById("log-a-session"), "inline-flex");
  }

  activities[0].location.onmouseover = function() {
    displayStats(activities[0]);
  }
  activities[1].location.onmouseover = function() {
    displayStats(activities[1]);
  }
  activities[2].location.onmouseover = function() {
    displayStats(activities[2]);
  }
  activities[3].location.onmouseover = function() {
    displayStats(activities[3]);
  }
  // activities[4].location.onmouseover = function() {
    // displayStats(activities[4]);
  // }
  for (var i = 0; i < activities.length; i++) {
    activities[i].location.onmouseout = function() {
      hideStats();
    }
  }


  function dailyHabitsFunctionality() {
    var habitElements = document.getElementsByClassName("habit");
    var imageElements = document.getElementsByClassName("habit-image");
    function HabitConstructFunction() {
      this.active = false;
    }
    var habits = [];
    for (var i = 0; i < habitElements.length; i++) {
      habits.push(new HabitConstructFunction());
      habits[i].element = habitElements[i];
      habits[i].imgElement = imageElements[i];
      habits[i].name = habits[i].element.getElementsByTagName('h3')[0].outerText;
    }
    for (var i = 0; i < imageElements.length; i++) {
      toggleFunctionality(habits[i].imgElement, habits[i].element, "1", habits[i]);
      hoverFunctionality(habits[i].imgElement, habits[i].element, "1", habits[i]);
      mouseOutFunctionality(habits[i].imgElement, habits[i].element, "0.1", habits[i]);
    }
  }
  function hoverFunctionality(triggerLocation, targetLocation, newOpacity, habitObject) {
    triggerLocation.onmouseover = function() {
      if (habitObject.active === false) {
        targetLocation.style.opacity = newOpacity;
      }
    }
  }
  function mouseOutFunctionality(triggerLocation, targetLocation, newOpacity, habitObject) {
    triggerLocation.onmouseout = function() {
      if (habitObject.active === false) {
        targetLocation.style.opacity = newOpacity;
      }
    }
  }
  function toggleFunctionality(triggerLocation, targetLocation, newOpacity, habitObject) {
    triggerLocation.onclick = function() {
      targetLocation.style.opacity = newOpacity;
      if (habitObject.active) {
        habitObject.active = false;
      }
      else {
        habitObject.active = true;
      }
    }
  }
  dailyHabitsFunctionality();

});

$("svg").click(function(e) {
	$("a.coords").html(e.pageX + "," + (e.pageY - 48));
	console.log(e.pageX);
});
	
