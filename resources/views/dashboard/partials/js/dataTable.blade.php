<!-- DataTables  & Plugins -->
<script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.10.24/js/dataTables.bootstrap4.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.2.7/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.2.7/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.7.0/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.7.0/js/buttons.bootstrap4.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.7.0/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.7.0/js/buttons.print.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.7.0/js/buttons.colVis.min.js"></script>

<script>
    $(function () {
        $("#data_table").DataTable({
            language: {
                url: '{{asset('assets/lang/datatable.ru.json')}}'
            },
            "responsive": true, "lengthChange": false, "autoWidth": false
        }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');

        if ($("#data_table_1")) {
            $("#data_table_1").DataTable({
                language: {
                    url: '{{asset('assets/lang/datatable.ru.json')}}'
                },
                "responsive": true, "lengthChange": false, "autoWidth": false
            }).buttons().container().appendTo('#example2_wrapper .col-md-6:eq(0)');
        }

        if ($("#data_table_2")) {
            $("#data_table_2").DataTable({
                language: {
                    url: '{{asset('assets/lang/datatable.ru.json')}}'
                },
                "responsive": true, "lengthChange": false, "autoWidth": false
            }).buttons().container().appendTo('#example2_wrapper .col-md-6:eq(0)');
        }

        $('body').on('click', '.delete-item', deleteItem);

        function deleteItem() {
            $('#delete_form').attr('action', $(this).data('action'))
        }
    });
</script>
