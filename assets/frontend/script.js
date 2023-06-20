jQuery( function( $ )  {

    $(document).ready( function() {
        $(document).on('submit', '.films-filter-form',function(e) {
            e.preventDefault();
            var data = $(this).serialize();
            console.log(data);
            $.ajax({
                url:VARS.ajax_url,
                type:'POST',
                data:data,
                success:function(response) {
                    $('.wj-films').html(response)
                }
            })
        } );
    } );

});