<script type="text/javascript">
    $(function() {

        // ADD new student
        $("#new_student_adding").submit(function(e) {
            e.preventDefault();
            const fd = new FormData(this);
            $("#add").text('Adding...');
            $.ajax({
                url: "{{ route('student.store') }}",
                method: 'post',
                data: fd,
                cache: false,
                contentType: false,
                processData: false,
                dataType: 'json',
                success: function(response) {
                    Swal.fire(
                        response.success ? 'Success' : 'Error',
                        response.success ? response.message : 'Competitor Id already exists!',
                        response.success ? 'success' : 'error'
                    );
                    if (response.success) {
                        fetchAllStudents(); // refresh table
                        $("#new_student_adding")[0].reset();
                        avg.value = '0.00'; // reset average
                    }
                    $("#add").text('Add');
                },
                error: function(xhr) {
                    $("#add").text('Add');
                    Swal.fire('Error', xhr.responseJSON?.message ?? 'Something went wrong', 'error');
                }
            });
        });

        // EDIT student (open modal and fill data)
        $(document).on('click', '.editIcon', function(e) {
            e.preventDefault();
            let Competitor_no = $(this).attr('id');
            $.ajax({
                url: "{{ route('student.edit') }}",
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

                },
            });
        });

        //fetch all competitors 
        fetchAllStudents();

        // FETCH all students
        function fetchAllStudents() {
            $.ajax({
                url: "{{ route('student.fetchAll') }}",
                method: 'get',
                success: function(response) {
                    $("#student").html(response);
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

        // update student (competitor)
        $("#edit_competitor_form").submit(function(e) {
            e.preventDefault();
            const fd = new FormData(this);
            $("#edit_competitor_btn").text('Updating...');
            $.ajax({
                url: "{{ route('student.update') }}",
                method: 'post',
                data: fd,
                cache: false,
                contentType: false,
                processData: false,
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        Swal.fire(response.title,
                            response.message,
                            response.type,
                        )
                        fetchAllStudents(); // refresh the table
                        $("#edit_competitor_form")[0].reset();
                        $("#editCompetitorModel").modal('hide'); // close modal
                    } else {
                        Swal.fire(
                            response.title,
                            response.message,
                            response.type
                        )
                    }
                    $("#edit_competitor_btn").text('Update');
                }

            });
        });




        // DELETE student
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
                        url: "{{ route('student.delete') }}",
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
            })
        });

    });
</script>