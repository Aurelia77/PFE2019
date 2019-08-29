// {# Temps total du track #}
$(function() {
    $('.audio-player').each(function() {

        var player =   $( this ).find( "audio" );
        $( this ).find( ".total-time" ).html(formatTime(player[0].duration));



    });
});

function formatTime(time) {
    const min = Math.floor(time / 60);
    const sec = Math.floor(time % 60);
    return min + ':' + ((sec < 10) ? ('0' + sec) : sec);
}