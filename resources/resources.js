function ajaxPostUpdate(phpPage, jsonPostData, troubleshooting = false) { 
            if (troubleshooting) {
                $.post( phpPage, jsonPostData , function() {
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
                alert( response );
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