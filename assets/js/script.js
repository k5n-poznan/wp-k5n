jQuery(document).ready(function ($) {
    $("#wpk5n-subscribe #wpk5n-submit").click(function () {
        $("#wpk5n-result").hide();

        subscriber = new Array();
        subscriber['name'] = $("#wpk5n-name").val();
        subscriber['surname'] = $("#wpk5n-surname").val();
        subscriber['mobile'] = $("#wpk5n-mobile").val();
        subscriber['groups'] = $("#wpk5n-groups").val();
        subscriber['type'] = $('input[name=subscribe_type]:checked').val();

        $("#wpk5n-subscribe").ajaxStart(function () {
            $("#wpk5n-submit").attr('disabled', 'disabled');
            $("#wpk5n-submit").text("Przetwarzanie...");
        });

        $("#wpk5n-subscribe").ajaxComplete(function () {
            $("#wpk5n-submit").removeAttr('disabled');
            $("#wpk5n-submit").text("Wyślij");
        });

        $.post(ajax_object.ajaxurl, {
            widget_id: $('#wpk5n-widget-id').attr('value'),
            action: 'subscribe_ajax_action',
            name: subscriber['name'],
            surname: subscriber['surname'],
            mobile: subscriber['mobile'],
            group: subscriber['groups'],
            type: subscriber['type'],
            nonce: ajax_object.nonce
        }, function (data, status) {

            var response = $.parseJSON(data);

            if (response.status == 'error') {
                $("#wpk5n-result").fadeIn();
                $("#wpk5n-result").html('<span class="wpk5n-message-error">' + response.response + '</div>');
            }

            if (response.status == 'success') {
                $("#wpk5n-result").fadeIn();
                $("#wpk5n-step-1").hide();
                $("#wpk5n-result").html('<span class="wpk5n-message-success">' + response.response + '</div>');
            }

            if (response.action == 'activation') {
                $("#wpk5n-step-2").show();
            }

        });

    });

    $("#wpk5n-subscribe #activation").on('click', function () {
        $("#wpk5n-result").hide();
        subscriber['activation'] = $("#wpk5n-ativation-code").val();

        $("#wpk5n-subscribe").ajaxStart(function () {
            $("#activation").attr('disabled', 'disabled');
            $("#activation").text('Loading...');
        });

        $("#wpk5n-subscribe").ajaxComplete(function () {
            $("#activation").removeAttr('disabled');
            $("#activation").text('Activation');
        });

        $.post(ajax_object.ajaxurl, {
            widget_id: $('#wpk5n-widget-id').attr('value'),
            action: 'activation_ajax_action',
            mobile: subscriber['mobile'],
            activation: subscriber['activation'],
            nonce: ajax_object.nonce
        }, function (data, status) {
            var response = $.parseJSON(data);

            if (response.status == 'error') {
                $("#wpk5n-result").fadeIn();
                $("#wpk5n-result").html('<span class="wpk5n-message-error">' + response.response + '</div>');
            }

            if (response.status == 'success') {
                $("#wpk5n-result").fadeIn();
                $("#wpk5n-step-2").hide();
                $("#wpk5n-result").html('<span class="wpk5n-message-success">' + response.response + '</div>');
            }
        });
    });
});