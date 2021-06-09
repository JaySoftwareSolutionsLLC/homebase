$('div.stat i.fa-info').on('click', function (event) {
    let info = $(this).attr('data-stat-description');
    showModal(event, info);
});
/*
$('div#stat-age-60-adw div.variant-row button').each(function (index, element) {
    if ($(this).attr('data-val') == '7') {
        $(this).addClass('active');
    }    
});
*/
$('div#stat-age-60-adw div.variant-row button').on('click', function () {
    let buttonEl = $(this);
    let contentEl = buttonEl.parents('div.variant-row').siblings('h4.main-metric-val');
    let exproi = buttonEl.attr('data-val');
    contentEl.html('...');
    // Redo calculation for age 60 adw and replace html with new value
    $.ajax({
        type: "POST",
        url: "/homebase/resources/ajax/variant_age_60_adw.php",
        data: {
            exproi: exproi
        },
        success: function (response) {
            $('div#stat-age-60-adw div.variant-row button').each(function () {
                $(this).removeClass('active');
            });
            buttonEl.addClass('active');
            contentEl.html(response);
        }
    });
});
$('div#stat-ade div.variant-row button').on('click', function() {
    let buttonEl = $(this);
    let contentEl = buttonEl.parents('div.variant-row').siblings('h4.main-metric-val');
    let category = buttonEl.attr('data-val');
    console.log(`${category}`);
    contentEl.html('...');
    // Redo calculation for ade and replace html with new value
    $.ajax({
        type: "POST",
        url: "/homebase/resources/ajax/variant_ade.php",
        data: {
            category: category,
            sd: sd,
            ed: ed
        },
        success: function (response) {
            $('div#stat-ade div.variant-row button').each(function () {
                $(this).removeClass('active');
            });
            buttonEl.addClass('active');
            contentEl.html(response);
        }
    });
});
$('div#stat-adi div.variant-row button').on('click', function() {
    let buttonEl = $(this);
    let contentEl = buttonEl.parents('div.variant-row').siblings('h4.main-metric-val');
    let category = buttonEl.attr('data-val');
    console.log(`${category}`);
    contentEl.html('...');
    // Redo calculation for adi and replace html with new value
    $.ajax({
        type: "POST",
        url: "/homebase/resources/ajax/variant_adi.php",
        data: {
            category : category,
            sd : sd,
            ed : ed
        },
        success: function (response) {
            $('div#stat-adi div.variant-row button').each(function () {
                $(this).removeClass('active');
            });
            buttonEl.addClass('active');
            contentEl.html(response);
        }
    });
});
$('div#stat-ff div.variant-row button').on('click', function() {
    let buttonEl = $(this);
    let mainContentEl = buttonEl.parents('div.variant-row').siblings('h4.main-metric-val');
    let subContentEl = buttonEl.parents('div.variant-row').siblings('h5');
    let withdrawalRate = buttonEl.attr('data-val');
    console.log(`${withdrawalRate} | ${unreceivedATI} | ${ed}`);
    mainContentEl.html('...');
    subContentEl.html('...');
    // Redo calculation for adi and replace html with new value
    $.ajax({
        type: "POST",
        url: "/homebase/resources/ajax/variant_days_ff.php",
        dataType: "JSON",
        data: {
            withdrawalRate : withdrawalRate,
            unreceivedATI: unreceivedATI,
            date : ed
        },
        success: function (responseJSON) {
            $('div#stat-ff div.variant-row button').each(function () {
                $(this).removeClass('active');
            });
            buttonEl.addClass('active');
            // console.log(responseJSON);
            mainContentEl.html(responseJSON['date']);
            subContentEl.html(`(${responseJSON['days']})`);
        }
    });
});
$('div#stat-theoretical-income div.variant-row button').each(function() {
    let buttonEl = $(this);
    let defaultVals = ['SD', 'RPM', 'SPM'];
    let dataAttr = buttonEl.attr('data-val');
    // console.log(dataAttr);
    if (defaultVals.indexOf(dataAttr) != -1) {
        buttonEl.addClass('active');
    }
});
$('div#stat-theoretical-income div.variant-row button').on('click', function() {
    let buttonEl = $(this);
    buttonEl.toggleClass('active');
    let mainContentEl = buttonEl.parents('div.variant-row').siblings('h4.main-metric-val');
    let params = [];
    $('div#stat-theoretical-income div.variant-row button').each(function() {
        if ($(this).hasClass('active')) {
            params.push($(this).attr('data-val'));
        }
    });
    mainContentEl.html('...');
    $.ajax({
        type: "POST",
        url: "/homebase/resources/ajax/variant_theoretical_income.php",
        dataType: "JSON",
        data: {
            params : params,
        }
        })
        .done(function(responseJSON) {
            console.log(responseJSON);
            mainContentEl.html(responseJSON['theoretical_future_income']);
        })
        .fail(function(error) {
            console.log(error);
        });
});
function returnTheoreticalIncome() {
    // Gather specifics of request
    // Working at Seal & Design?
    // Shifts at Ricks
    // Make request to endpoint
}