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
$('div#stat-ade div.variant-row button').on('click', function () {
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
$('div#stat-adi div.variant-row button').on('click', function () {
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
            category: category,
            sd: sd,
            ed: ed
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