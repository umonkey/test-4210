/**
 * Task list animation.
 **/

jQuery(function ($) {
    var em = $('table.tasklist:first');

    if (em.length == 0) {
        return;
    }

    var table = em.DataTable({
        'processing': true,
        'pageLength': 3,
        'servecSide': true,
        'lengthChange': false,
        'searching': false,
        'ajax': '/tasks.json'
    });

    $(document).on('submit', 'form.async', function (e) {
        var url, method, form;

        form = $(this);
        url = $(this).attr('action');
        method = $(this).attr('method');

        $.ajax({
            url: url,
            dataType: 'json',
            data: $(this).serialize(),
            type: method
        }).done(function (res) {
            if (res.error) {
                alert(res.error);
            } else if (res.result) {
                if (res.result.refresh) {
                    form[0].reset();
                    table.ajax.reload();
                }

                if (res.result.message) {
                    alert(res.result.message);
                }
            }
        }).fail(function () {
            alert('Не удалось добавить задачу.');
        });

        e.preventDefault();
    });
});
