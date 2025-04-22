function show_add_po_modal() {
    $modal = $('#add_po_modal');
    $modal.modal('loading').modal('show');

    distributor_id = $('#distributor_id').val();

    $.ajax({
        url: '/get/po-create',
        type: 'get',
        dataType: 'html',
        data: {
            distributor_id : distributor_id
        },
    })
    .done(function(html) {
        console.log("success");

        if (html)
            $modal.find('.modal-body').html(html);

        $modal.modal('loading');
        initSearchOptionDistributor('distributor_id_modal', 'SearchBox_modal');

        $('#save_po_btn').click(save_po);

        $modal.on('hidden', function () {
            $('#save_po_btn').unbind('click');
        })
    })
    .fail(function() {
        console.log("error");
    })
    .always(function() {
        console.log("complete");
    });

}

function save_po() {
    $('#info_modal').text('');
    //distributor_id = $('#distributor_id_modal').val();
    po_name = $('#po_name_modal').val();
    note_modal = $('#note_modal').val();

    /*
    if (!distributor_id) {
        $('#info_modal').text('Vui lòng chọn Distributor');
        return false;
    }
    */
    if (!po_name) {
        $('#info_modal').text('Vui lòng điền PO Number');
        return false;
    }

    $.ajax({
        url: '/get/po-save',
        type: 'post',
        dataType: 'json',
        data: {
            //distributor_id : distributor_id,
            po_name : po_name,
            note : note_modal
        },
    })
    .done(function(data) {
        console.log("success");

        if (data) {
            if (data.code != 1 && data.error) {
                $('#info_modal').text(data.error);
                return false;
            } else {
                $('#info_modal').text('');

                get_po_list(distributor_id, "#distributor_po", function() {
                    $modal.modal('loading');
                    $modal.modal('hide');
                });
            }
        } else {
            $('#info_modal').text('Error!!!');
            return false;
        }
    })
    .fail(function() {
        console.log("error");
    })
    .always(function(){
        console.log("complete");
    });

}

function get_po_list(distributor_id, element_id, callback) {
    $('#distributor_po').find('option:not(:first)').remove();
    $('#distributor_po').find('option:first').text('Loading PO...');

    $.ajax({
        url: '/get/po-list',
        type: 'get',
        dataType: 'html',
        data: {
            //distributor_id: distributor_id
        },
    })
    .done(function(html) {
        console.log("success");

        if (html)
            $(element_id).html(html);

        if (typeof callback === 'function')
            callback();

        $('#distributor_po').val(distributor_id);
    })
    .fail(function() {
        console.log("error");
    })
    .always(function() {
        console.log("complete");
    });


}

function show_po_note(e) {
    _self = $(e.target).find('option:selected');

    try {
        note = _self.data('note');
        if (note) 
            $('#po_note').text(note).show();
        else
            $('#po_note').text('').hide();
    } catch (err) {}
}

(function($) {
    $('#distributor_po').change(show_po_note);

})(jQuery);