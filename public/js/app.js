/**
 * Task list animation.
 **/

jQuery(function ($) {
    var em, table;

    em = $('table.tasklist:first');
    if (em.length == 1) {
        table = em.DataTable({
            'processing': true,
            'pageLength': 3,
            'servecSide': true,
            'lengthChange': false,
            'searching': false,
            'ajax': '/tasks.json',
            'columnDefs': [{
                'render': function (data, type, row) {
                    return row[4] ? "<a href='" + row[4] + "'>edit</a>" : "";
                },
                'targets': 4
            }]
        });
    }


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
                    if (table) {
                        table.ajax.reload();
                    }
                    return;
                }

                else if (res.result.redirect) {
                    window.location.href = res.result.redirect;
                    return;
                }

                if (res.result.message) {
                    alert(res.result.message);
                }
            }
        }).fail(function () {
            alert('Не удалось обработать форму.');
        });

        e.preventDefault();
    });
});
