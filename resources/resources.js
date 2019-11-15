// Resources page is used to instantiate functions which may be used by multiple different sections throughout the site

//LOAD DEPENDENCIES
//$.getScript("/homebase/resources/js/jquery-ui.js", function () { });
$.getScript("/homebase/resources/js/moment.js", function () { });

function ajaxPostUpdate(phpPage, jsonPostData, troubleshooting = false) { 
            if (troubleshooting) {
                $.post( phpPage, jsonPostData , function() {
                    $('p.notifications').html('Success!');
                    alert( "success");
                } )
                .done(function( response ) {
                    alert( "second success" );
                    alert( response );
                })
                .fail(function() {
                    alert( "error" );
                    alert( response );
                })
                .always(function() {
                    alert( "finished" ); 
                    alert( response );
                });
           }
           else {
            $.post( phpPage, jsonPostData , function() { } )
            .done(function( response ) {
                //alert( response );
                $('p.user-feedback').append( response );
                if ( $('p.user-feedback').css('display') == 'none') {
                    $('p.user-feedback').css('display', 'inline');
                }
            })
            }
        };
function elegantRounding(number, precision) {
    let i = 0;
    let factor = 1;
    for (i; i < precision; i++) {
        factor *= 10;
    }
    console.log(factor);
    return Math.round( number * factor ) / (factor);
}
function showModal(event, content) {
    event.stopPropagation();
    console.log('show modal');
    $('div#modal').html(content);
    $('div#modal-housing').css('display', 'flex');
}
function hideModal() {
    console.log('hide modal');
    $('div#modal').empty();
    $('div#modal-housing').css('display', 'none');
}
function getCurrentDate() { // Used on weather.js currently
    return new Date();
}

// Predefined dates populates a set of inputs with dates
function populatePredefinedDates(selVal, dateElementStart, dateElementEnd, dateFormat = 'YYYY-MM-DD') {

    var dateRange = selVal;

    var today = moment().format(dateFormat);
    var sd = moment();	//start date
    var ed = moment();	//end date

    if (dateRange != "none") {

        switch (dateRange) {

            case "yesterday":
                sd.subtract(1, 'days');
                ed.subtract(1, 'days');
                break;

            case "day before yesterday":
                sd.subtract(2, 'days');
                ed.subtract(2, 'days');
                break;

            case "7 days":
                sd.subtract(7, 'days');
                break;

            case "30 days":
                sd.subtract(30, 'days');
                break;

            case "90 days":
                sd.subtract(90, 'days');
                break;

            case "180 days":
                sd.subtract(180, 'days');
                break;

            case "365 days":
                sd.subtract(365, 'days');
                break;

            case "this week":
                sd.startOf('week');
                break;

            case "this month":
                sd.startOf('month');
                break;

            case "this year":
                sd.startOf('year');
                break;

            case "last week":
                sd.subtract(1, 'week').startOf('week');
                ed.subtract(1, 'week').endOf('week');
                break;

            case "last month":
                sd.subtract(1, 'month').startOf('month');
                ed.subtract(1, 'month').endOf('month');
                break;

            case "last year":
                sd.subtract(1, 'year').startOf('year');
                ed.subtract(1, 'year').endOf('year');
                break;

            case "year before last":
                sd.subtract(2, 'year').startOf('year');
                ed.subtract(2, 'year').endOf('year');
                break;

            case "two years before last":
                sd.subtract(3, 'year').startOf('year');
                ed.subtract(3, 'year').endOf('year');
                break;

            case "week before last":
                sd.subtract(2, 'week').startOf('week');
                ed.subtract(2, 'week').endOf('week');
                break;

            case "month before last":
                sd.subtract(2, 'month').startOf('month');
                ed.subtract(2, 'month').endOf('month');
                break;

            case "last 3 months":
                sd.subtract(3, 'month').startOf('month');
                ed.subtract(1, 'month').endOf('month');
                break;

            case "last 6 months":
                sd.subtract(6, 'month').startOf('month');
                ed.subtract(1, 'month').endOf('month');
                break;

            case "through end of week":
                sd = moment('2018-08-28', 'YYYY-MM-DD')
                ed.endOf('week').subtract(1, 'day');
                break;

            case "through next week":
                sd = moment('2018-08-28', 'YYYY-MM-DD')
                ed.add(1, 'week').endOf('week').subtract(1, 'day');
                break;

            case "through end of month":
                sd = moment('2018-08-28', 'YYYY-MM-DD')
                ed.endOf('month');
                break;

            case "through next month":
                sd = moment('2018-08-28', 'YYYY-MM-DD')
                ed.add(1, 'month').endOf('month');
                break;

            case "through end of quarter":
                sd = moment('2018-08-28', 'YYYY-MM-DD')
                ed.endOf('quarter');
                break;

            // For bucket reports
            // Test scenerio: run on November 3rd 2018

            // Result should be Nov 1st 2017 - April 30th 2018
            case "6mo ago to 1yr ago":
                sd.subtract(12, 'month').startOf('month');
                ed.subtract(7, 'month').endOf('month');
                break;

            // Result should be May 1st 2017 - October 31st 2017
            case "1yr ago to 18mo ago":
                sd.subtract(18, 'month').startOf('month');
                ed.subtract(13, 'month').endOf('month');
                break;

            // Result should be Nov 1st 2016 - April 30st 2017
            case "18mo ago to 2yr ago":
                sd.subtract(24, 'month').startOf('month');
                ed.subtract(19, 'month').endOf('month');
                break;

            // Result should be < October 31st 2016
            case "more than 2yr ago":
                ed.subtract(25, 'month').endOf('month');
                break;

            case "this quarter":
                sd.startOf('quarter');
                break;

            case "last quarter":
                sd.subtract(1, 'quarter').startOf('quarter');
                ed.subtract(1, 'quarter').endOf('quarter');
                break;

            case "plus one day":
                ed.add(1, 'day');
                break;

            case "plus two days":
                ed.add(2, 'days');
                break;

            case "plus three days":
                ed.add(3, 'days');
                break;

            case "plus four days":
                ed.add(4, 'days');
                break;

            case "plus five days":
                ed.add(5, 'days');
                break;

        } //end switch

    }

    if (dateRange == "all" || dateRange == "none") {
        $("#" + dateElementStart).siblings("input[type=checkbox]").prop("checked", true);
        $("#" + dateElementStart).val("");
        $("#" + dateElementEnd).val("");
    } else {
        $("#" + dateElementStart).siblings("input[type=checkbox]").prop("checked", false);
        $("#" + dateElementStart).val(sd.format(dateFormat));
        $("#" + dateElementEnd).val(ed.format(dateFormat));
    }

} //end function populatePredefinedDates

// Sets an element's value to the current UTC datetime
$.fn.setNow = function (onlyBlank) {
    var now = new Date($.now());

    var year = now.getFullYear();

    var month = (now.getMonth() + 1).toString().length === 1 ? '0' + (now.getMonth() + 1).toString() : now.getMonth() + 1;
    var date = now.getDate().toString().length === 1 ? '0' + (now.getDate()).toString() : now.getDate();
    var hours = now.getHours().toString().length === 1 ? '0' + now.getHours().toString() : now.getHours();
    var minutes = now.getMinutes().toString().length === 1 ? '0' + now.getMinutes().toString() : now.getMinutes();
    var seconds = now.getSeconds().toString().length === 1 ? '0' + now.getSeconds().toString() : now.getSeconds();

    var formattedDateTime = year + '-' + month + '-' + date + 'T' + hours + ':' + minutes + ':' + seconds;

    if (onlyBlank === true && $(this).val()) {
        return this;
    }

    $(this).val(formattedDateTime);

    return this;
}