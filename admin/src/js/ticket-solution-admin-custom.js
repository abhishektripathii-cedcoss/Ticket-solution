jQuery(document).ready(function($){

    $( ".my_custom_check_multiselect" ).select2();
    
    $('.sale_ticket_show').click(function(){
        $('.custom_set_stock_2_field').show();
        $('.custom_set_price_2_field').show();
    });
    $('.sale_ticket_hide').click(function(){
        $('.custom_set_stock_2_field').hide();
        $('.custom_set_price_2_field').hide();
    });

});