<script type="text/javascript">
    $(function() {

        function fetchAllStudents() {
            $.ajax({
                url: "{{ route('artcompetitors.fetchAll') }}",
                method: 'get',
                success: function(response) {
                    $("#student").html(response);

                    // Destroy existing DataTable before reinitializing
                    if ($.fn.DataTable.isDataTable("table")) {
                        $("table").DataTable().destroy();
                    }

                    $("table").DataTable({
                        order: [0, 'asc'],
                        scrollY: '45vh',
                        scrollCollapse: true,
                        paging: false,
                        info: false,
                        columnDefs: [{
                            orderable: false,
                            targets: [4, 6]
                        }]
                    });
                }
            });
        }

        fetchAllStudents();

        //Add new competitor
        $("#new_competitor_adding").submit(function(e) {
            e.preventDefault();
            const fd = new FormData(this);

            if (!fd.get('art_file')) {
                Swal.fire({
                    icon: 'error',
                    title: 'Oops!',
                    text: 'No file selected!',
                    confirmButtonColor: '#d33',
                    timer: 2500
                });
                return;
            }

            $("#add").text('Adding...');

            $.ajax({
                url: "{{ route('artcompetitors.store') }}",
                method: 'post',
                data: fd,
                cache: false,
                contentType: false,
                processData: false,
                dataType: 'json',
                success: function(response) {
                    Swal.fire({
                        icon: response.type,
                        title: response.title,
                        text: response.message,
                        confirmButtonColor: '#3085d6',
                        timer: 2500
                    });

                    if (response.success) {
                        fetchAllStudents();
                        $("#new_competitor_adding")[0].reset();
                    }

                    $("#add").text('Add');
                },
                error: function(xhr) {
                    $("#add").text('Add');
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: xhr.responseJSON?.message ?? 'Something went wrong.',
                        confirmButtonColor: '#d33',
                        timer: 2500
                    });
                }
            });
        });

        //edit model onclick
        $(document).on('click', '.editIcon', function(e) {
            e.preventDefault();
            let Competitor_no = $(this).attr('id');

            $.ajax({
                url: "{{ route('artcompetitors.edit') }}",
                method: 'get',
                data: {
                    id: Competitor_no,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    $("#competitor_id").val(response.id); // hidden field
                    $("#edit_competitor_id").val(response.Competitor_no);
                    $("#edit_competitor_name").val(response.Name);
                    $("#edit_competitor_school").val(response.School);
                    $("#edit_competitor_incharge").val(response.Incharge);
                }
            });
        });

        //Edit competitor
        $("#edit_competitor_form").submit(function(e) {
            e.preventDefault();
            const fd = new FormData(this);

            $("#edit_competitor_btn").text('Updating...');

            $.ajax({
                url: "{{ route('artcompetitors.update') }}",
                method: 'post',
                data: fd,
                cache: false,
                contentType: false,
                processData: false,
                dataType: 'json',
                success: function(response) {

                    Swal.fire(response.title, response.message, response.type);

                    if (response.success) {
                        fetchAllStudents();
                        $("#edit_competitor_form")[0].reset();
                        $("#editCompetitorModel").modal('hide');
                    }

                    $("#edit_competitor_btn").text('Update');
                }
            });
        });

        //view image
        function viewCompetitorImage(competitorId) {
            $.ajax({
                url: "{{ route('artcompetitors.getImage') }}",
                method: 'get',
                data: {
                    id: competitorId,
                    _token: '{{ csrf_token() }}'
                },
                dataType: 'json',
                success: function(response) {
                    if (response.success && response.image_url) {
                        $("#viewImage").attr('src', response.image_url);
                        $("#viewImageModal").modal('show');
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops!',
                            text: 'Image not found!',
                            confirmButtonColor: '#d33',
                            timer: 2500
                        });
                    }
                },
                error: function(xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: xhr.responseJSON?.message ?? 'Something went wrong.',
                        confirmButtonColor: '#d33',
                        timer: 2500
                    });
                }
            });
        }

        // Event listener
        $(document).on('click', '.view-img-icon', function() {
            let competitorId = $(this).data('id'); // use data-id attribute
            viewCompetitorImage(competitorId);
        });

        // Delete competitor
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
                        url: "{{ route('artcompetitors.delete') }}",
                        method: 'delete',
                        data: {
                            id: id,
                            _token: csrf
                        },
                        success: function(response) {
                            Swal.fire('Deleted!', 'Competitor has been deleted.', 'success');
                            fetchAllStudents();
                        }
                    });
                }
            });
        });

    });
</script>