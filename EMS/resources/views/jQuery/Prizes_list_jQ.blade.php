<script type="text/javascript">
    $(function() {
        // add new prize
        $("#new_prize_adding").submit(function(e) {
            e.preventDefault();
            const fd = new FormData(this);
            $("#add_prize_btn").text('Adding...');
            $.ajax({
                url: "{{ route('prizes.store') }}",
                method: 'post',
                data: fd,
                cache: false,
                contentType: false,
                processData: false,
                dataType: 'json',
                success: function(response) {
                    Swal.fire(
                        response.success ? 'Success' : 'Error',
                        response.success ? response.message : 'Prize no already exists!',
                        response.success ? 'success' : 'error'
                    );
                    if (response.success) {
                        fetchAllPrizes();

                        $("#new_prize_adding")[0].reset();
                    }
                    $("#add").text('Add');
                },
                error: function(xhr) {
                    $("#add").text('Add');
                    Swal.fire('Error', xhr.responseJSON?.message ?? 'Something went wrong', 'error');
                }
            });
        });

        // edit prize
        $(document).on('click', '.editIcon', function(e) {
            e.preventDefault();
            let prize_id = $(this).attr('id');

            $.ajax({
                url: "{{ route('prizes.edit') }}",
                method: 'get',
                data: {
                    id: prize_id
                },
                success: function(response) {
                    $("#prize_id").val(response.id); // hidden input for update
                    $("#edit_prize_id").val(response.Position); // corrected
                    $("#edit_prize_amount").val(response.Prize_amount);
                    $("#editPrizeModel").modal('show'); // show the modal
                },
                error: function() {
                    Swal.fire('Error', 'Could not fetch prize data', 'error');
                }
            });
        });


        // fetch all prizes
        fetchAllPrizes();

        function fetchAllPrizes() {
            $.ajax({
                url: "{{ route('prizes.fetchAll') }}",
                method: 'get',
                success: function(response) {
                    $("#prizes").html(response.data); // <- response.data, not the full response
                    $("table").DataTable({
                        order: [0, 'asc'],
                        scrollY: '45vh',
                        scrollCollapse: true,
                        paging: false,
                        info: false,
                        columnDefs: [{
                            orderable: false,
                            targets: [1, 2, 3]
                        }]
                    });
                }

            });
        }

        // update prize
        $("#edit_prize_form").submit(function(e) {
            e.preventDefault();
            const fd = new FormData(this);
            $("#edit_prize_btn").text('Updating...');
            $.ajax({
                url: "{{ route('prizes.update') }}",
                method: 'post',
                data: fd,
                cache: false,
                contentType: false,
                processData: false,
                dataType: 'json',
                success: function(response) {
                    Swal.fire(response.title, response.message, response.type);
                    if (response.success) {
                        Swal.fire(response.title,
                            response.message,
                            response.type,
                        )
                        fetchAllPrizes();
                        $("#edit_prize_form")[0].reset();
                        $("#editPrizeModel").modal('hide');
                    } else {
                        Swal.fire(
                            response.title,
                            response.message,
                            response.type
                        )
                    }
                    $("#edit_prize_btn").text('Update');
                }
            });
        });

        // delete prize
        $(document).on('click', '.deleteIcon', function(e) {
            e.preventDefault();
            let id = $(this).attr('id');
            let csrf = '{{ csrf_token() }}';
            Swal.fire({
                title: 'Are you sure?',
                text: "This will be deleted!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ route('prizes.delete') }}",
                        method: 'post',
                        data: {
                            id: id,
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            Swal.fire('Deleted!', 'Prize has been deleted.', 'success');
                            fetchAllPrizes();
                        }
                    });

                }
            })
        });
    });
</script>