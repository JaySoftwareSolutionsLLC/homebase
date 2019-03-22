$('div.stat i.fa-info').on('click', function( event ) {
    let info = $(this).attr('data-stat-description');
    showModal(event, info);
});