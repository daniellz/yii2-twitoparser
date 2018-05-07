$(document).ready(function(){
    $('.twits-index').on('click', '.send-tg', function(){
        // console.log('/send-tg?id='+$(this).data('id'));
        $.get('send-tg?id='+$(this).data('id'));
    });
    $('.translate-twit').on('click', function(){
        var $btn = $(this);
        $.get('translate?id='+$btn.data('id')).done(function(ans){
            $('tr[data-key="'+$btn.data('id')+'"]').find('td').eq(5).text(ans);
            $btn.remove();
        });
    });
});