$(document).ready(function() {
    $('#btn-add').click(function(){
        $('#all-courses option:selected').each( function() {
                $('#selected-courses').append("<option value='"+$(this).val()+"'>"+$(this).text()+"</option>");
            $(this).remove();
        });
    });
    $('#btn-remove').click(function(){
        $('#selected-courses option:selected').each( function() {
            $('#all-courses').append("<option value='"+$(this).val()+"'>"+$(this).text()+"</option>");
            $(this).remove();
        });
    });
    $('#btn-up').bind('click', function() {
        $('#selected-courses option:selected').each( function() {
            var newPos = $('#selected-courses option').index(this) - 1;
            if (newPos > -1) {
                $('#selected-courses option').eq(newPos).before("<option value='"+$(this).val()+"' selected='selected'>"+$(this).text()+"</option>");
                $(this).remove();
            }
        });
    });
    $('#btn-down').bind('click', function() {
        var countOptions = $('#selected-courses option').length;
        $('#selected-courses option:selected').each( function() {
            var newPos = $('#selected-courses option').index(this) + 1;
            if (newPos < countOptions) {
                $('#selected-courses option').eq(newPos).after("<option value='"+$(this).val()+"' selected='selected'>"+$(this).text()+"</option>");
                $(this).remove();
            }
        });
    });
});
