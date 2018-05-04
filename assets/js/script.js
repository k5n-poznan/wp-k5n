jQuery(document).ready(function ($) {
    $("#wpk5n-subscribe #wpk5n-subscribe-submit").click(function () {
        $("#wpk5n-subscribe-result").hide();

        subscriber = new Array();
        subscriber['name'] = $("#wpk5n-subscribe-name").val();
        subscriber['surname'] = $("#wpk5n-subscribe-surname").val();
        subscriber['mobile'] = $("#wpk5n-subscribe-mobile").val();
        subscriber['groups'] = $("#wpk5n-subscribe-groups").val();
        subscriber['type'] = $('input[name=subscribe_type]:checked').val();
        
        var widget_id = $('#wpk5n-widget-id').attr('value');

        $("#wpk5n-subscribe").ajaxStart(function () {
            $("#wpk5n-subscribe-submit").attr('disabled', 'disabled');
            $("#wpk5n-subscribe-submit").text("Przetwarzanie...");
        });

        $("#wpk5n-subscribe").ajaxComplete(function () {
            $("#wpk5n-subscribe-submit").removeAttr('disabled');
            $("#wpk5n-subscribe-submit").text("Rejestruj");
        });

        $.post(ajax_object.ajaxurl, {
            action: 'subscribe_ajax_action',
            widgetid: widget_id,
            name: subscriber['name'],
            surname: subscriber['surname'],
            mobile: subscriber['mobile'],
            group: subscriber['groups'],
            type: subscriber['type'],
            nonce: ajax_object.nonce
        }, function (data, status) {

            var response = $.parseJSON(data);

            if (response.status == 'error') {
                $("#wpk5n-subscribe-result").fadeIn();
                $("#wpk5n-subscribe-result").html('<span class="wpk5n-message-error">' + response.response + '</div>');
            }

            if (response.status == 'success') {
                $("#wpk5n-subscribe-result").fadeIn();
                $("#wpk5n-subscribe-step-1").hide();
                $("#wpk5n-subscribe-result").html('<span class="wpk5n-message-success">' + response.response + '</div>');
            }

            if (response.action == 'activation') {
                $("#wpk5n-subscribe-step-2").show();
            }

        });

    });

    $("#wpk5n-subscribe #activation").on('click', function () {
        $("#wpk5n-subscribe-result").hide();
        subscriber['activation'] = $("#wpk5n-ativation-code").val();

        var widget_id = $('#wpk5n-widget-id').attr('value');

        $("#wpk5n-subscribe").ajaxStart(function () {
            $("#activation").attr('disabled', 'disabled');
            $("#activation").text('Przetwarzanie...');
        });

        $("#wpk5n-subscribe").ajaxComplete(function () {
            $("#activation").removeAttr('disabled');
            $("#activation").text('Aktywacja');
        });

        $.post(ajax_object.ajaxurl, {
            action: 'activation_ajax_action',
            widgetid: widget_id,
            mobile: subscriber['mobile'],
            activation: subscriber['activation'],
            nonce: ajax_object.nonce
        }, function (data, status) {
            var response = $.parseJSON(data);

            if (response.status == 'error') {
                $("#wpk5n-subscribe-result").fadeIn();
                $("#wpk5n-subscribe-result").html('<span class="wpk5n-message-error">' + response.response + '</div>');
            }

            if (response.status == 'success') {
                $("#wpk5n-subscribe-result").fadeIn();
                $("#wpk5n-subscribe-step-2").hide();
                $("#wpk5n-subscribe-result").html('<span class="wpk5n-message-success">' + response.response + '</div>');
            }
        });
    });
});