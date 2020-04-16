<script type="text/javascript">
    (function (window, document, $, undefined) {

        $(function () {
            // For some browsers, `attr` is undefined; for others,
            // `attr` is false.  Check for both.
            if (ttable) {
                var newAtt = $("#" + attr).attr('id');
                var dtable = '[id=' + ttable + ']';
            } else {
                var dtable = '[id^=DataTables]';
            }
            var total_header = ($('table#DataTables th:last').index());
            var testvar = [];
            for (var i = 0; i < total_header; i++) {
                testvar[i] = i;
            }
            var length_options = [10, 25, 50, 100];
            var length_options_names = [10, 25, 50, 100];

            var tables_pagination_limit = <?= config_item('tables_pagination_limit')?>;
            if (tables_pagination_limit == '') {
                tables_pagination_limit = 10;
            }
            tables_pagination_limit = parseFloat(tables_pagination_limit);

            if ($.inArray(tables_pagination_limit, length_options) == -1) {
                length_options.push(tables_pagination_limit);
                length_options_names.push(tables_pagination_limit)
            }
            length_options.sort(function (a, b) {
                return a - b;
            });
            length_options_names.sort(function (a, b) {
                return a - b;
            });

            table = $(dtable).dataTable({
                'responsive': true,  // Table pagination
                "processing": true,
                "serverSide": true,
                "pageLength": tables_pagination_limit,
                "aLengthMenu": [length_options, length_options_names],
                'dom': 'lBfrtip',  // Bottom left status text
                buttons: [
                    {
                        extend: 'print',
                        text: "<i class='fa fa-print'> </i>",
                        className: 'btn btn-danger btn-xs mr',
                        exportOptions: {
                            format: {
                                body: function(data, column, row) {
                                    data = data.replace(/(<([^>]+)>)/ig,"");
                                    return $.trim(data);
                                }
                            },
                            columns: ':not(:last-child)',
                        }
                    },
                    {
                        extend: 'print',
                        text: "<i class='fa fa-print'> </i> &nbsp;<?= lang('selected')?>",
                        className: 'btn btn-success mr btn-xs',
                        exportOptions: {
                            format: {
                                body: function(data, column, row) {
                                    data = data.replace(/(<([^>]+)>)/ig,"");
                                    return $.trim(data);
                                }
                            },
                            modifier: {
                                selected: true,
                                columns: ':not(:last-child)',
                            }
                        }

                    },
                    {
                        extend: 'excel',
                        text: '<i class="fa fa-file-excel-o"> </i>',
                        className: 'btn btn-purple mr btn-xs',
                        exportOptions: {
                            format: {
                                body: function(data, column, row) {
                                    data = data.replace(/(<([^>]+)>)/ig,"");
                                    return $.trim(data);
                                }
                            },
                            columns: ':not(:last-child)',
                        }
                    },
                    {
                        extend: 'csv',
                        text: '<i class="fa fa-file-excel-o"> </i>',
                        className: 'btn btn-primary mr btn-xs',
                        exportOptions: {
                            format: {
                                body: function(data, column, row) {
                                    data = data.replace(/(<([^>]+)>)/ig,"");
                                    return $.trim(data);
                                }
                            },
                            columns: ':not(:last-child)',
                        }
                    },
                    {
                        extend: 'pdf',
                        text: '<i class="fa fa-file-pdf-o"> </i>',
                        className: 'btn btn-info mr btn-xs',
                        exportOptions: {
                            format: {
                                body: function(data, column, row) {
                                    data = data.replace(/(<([^>]+)>)/ig,"");
                                    return $.trim(data);
                                }
                            },
                            columns: ':not(:last-child)',
                        }
                    },
                    {
                        text: '<?= lang('bulk_delete')?>',
                        className: 'btn btn-xs btn-default custom-bulk-button',
                    },
                ],
                select: true,
                "order": [],
                "ajax": {
                    url: list,
                    type: "POST",
                    error: function (xhr, error, thrown) {
                        console.log(xhr.responseText);
                    },
                    data: function (d) {
                        d.csrf_token = getCookie('csrf_cookie');
                    },

                },
                'fnCreatedRow': function (nRow, aData, iDataIndex) {
                    $(nRow).attr('id', 'table_' + iDataIndex); // or whatever you choose to set as the id
                },
                // Text translation options
                // Note the required keywords between underscores (e.g _MENU_)
                oLanguage: {
                    sSearch: "<?= lang('search_all_column')?>",
                    sLengthMenu: "_MENU_",
                    zeroRecords: "<?= lang('nothing_found_sorry')?>",
                    infoEmpty: "<?= lang('no_record_available')?>",
                    infoFiltered: "(<?= lang('filtered_from')?> _MAX_ <?= lang('total')?> <?= lang('records')?>)"
                }


            });

        });

    })(window, document, window.jQuery);

    function getCookie(name) {
        var cookieValue = null;
        if (document.cookie && document.cookie != '') {
            var cookies = document.cookie.split(';');
            for (var i = 0; i < cookies.length; i++) {
                var cookie = jQuery.trim(cookies[i]);
                // Does this cookie string begin with the name we want?
                if (cookie.substring(0, name.length + 1) == (name + '=')) {
                    cookieValue = decodeURIComponent(cookie.substring(name.length + 1));
                    break;
                }
            }
        }
        return cookieValue;
    }

    function reload_table() {
        table.api().ajax.reload();
    }

    function table_url(url) {
        table.api().ajax.url(url).load();
    }
</script>

<script type="text/javascript">
    $(document).ready(function () {
        $('#datatable_action').dataTable({
            paging: false,
            "bSort": false,
            'dom': 'lBfrtip',  // Bottom left status text
            buttons: [
                {
                    extend: 'print',
                    text: "<i class='fa fa-print'> </i>",
                    className: 'btn btn-danger btn-xs mr',
                },
                {
                    extend: 'print',

                    text: "<i class='fa fa-print'> </i> &nbsp;<?= lang('selected')?>",
                    className: 'btn btn-success mr btn-xs',

                },
                {
                    extend: 'excel',
                    text: '<i class="fa fa-file-excel-o"> </i>',
                    className: 'btn btn-purple mr btn-xs',
                },
                {
                    extend: 'csv',
                    text: '<i class="fa fa-file-excel-o"> </i>',
                    className: 'btn btn-primary mr btn-xs',
                },
                {
                    extend: 'pdf',
                    text: '<i class="fa fa-file-pdf-o"> </i>',
                    className: 'btn btn-info mr btn-xs',
                },
            ],
            select: true,
        });
    });
</script>