$(document).ready(function(){
    $('.sexe').on('click', function() {
        $('.sexe').not(this).prop('checked', false);
        $('#result').html($(this).data( "id" ));
        if($(this).is(":checked"))
            $('#result').html($(this).data( "id" ));
        else
            $('#result').html('Empty...!');
    });
    $('.orientation').on('click', function() {
        $('.orientation').not(this).prop('checked', false);
        $('#result').html($(this).data( "id" ));
        if($(this).is(":checked"))
            $('#result').html($(this).data( "id" ));
        else
            $('#result').html('Empty...!');
    });
});

$(function () {
    $('.list-group.checked-list-box .list-group-item').each(function () {

        // Settings
        var $widget = $(this),
            $checkbox = $('<input type="checkbox" class="hidden" />'),
            color = ($widget.data('color') ? $widget.data('color') : "primary"),
            style = ($widget.data('style') == "button" ? "btn-" : "list-group-item-"),
            settings = {
                on: {
                    icon: 'glyphicon glyphicon-check'
                },
                off: {
                    icon: 'glyphicon glyphicon-unchecked'
                }
            };

        $widget.css('cursor', 'pointer')
        $widget.append($checkbox);

        // Event Handlers
        $widget.on('click', function () {
            $checkbox.prop('checked', !$checkbox.is(':checked'));
            $checkbox.triggerHandler('change');
            updateDisplay();
        });
        $checkbox.on('change', function () {
            updateDisplay();
        });
    });
});