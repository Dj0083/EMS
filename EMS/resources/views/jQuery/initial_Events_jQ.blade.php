<script type="text/javascript">
    $(function() {
        // fetch all event types ajax request
        fetchAllEventTypes();

        function fetchAllEventTypes() {
            $.ajax({
                url: "{{ route('event_type.fetchAll') }}",
                method: 'get',
                success: function(response) {
                    $("#event_types").html(response);
                    $("table").DataTable({
                        order: [0, 'asc'],
                        scrollY: '50vh',
                        scrollCollapse: true,
                        paging: false,
                        info: false,
                        columnDefs: [{
                            orderable: false,
                            targets: [1, 2, 3]
                        }]
                    });
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    console.error("Error occoured: ".textStatus);
                    console.error(errorThrown);
                }
            });
        }

        // add new event_type ajax request
        $("#new_event_type_form").submit(function(e) {
            e.preventDefault();
            const fd = new FormData(this);
            $("#save").text('Adding...');

            $.ajax({
                url: "{{ route('event_type.store') }}",
                method: 'post',
                data: fd,
                cache: false,
                contentType: false,
                processData: false,
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        Swal.fire(
                            response.title,
                            response.message,
                            response.type
                        )
                        fetchAllEventTypes();
                    } else {
                        Swal.fire(
                            response.title,
                            response.message,
                            response.type
                        )
                    }
                    $("#save").text('Add');
                    $("#new_event_type_form")[0].reset();
                }
            });
        });

        //show dedicated image for the event type
        $(document).on('click', '.imageIcon', function(e) {
            e.preventDefault();
            let id = $(this).attr('id');

            $.ajax({
                url: "{{ route('event_type.showImage') }}",
                method: 'get',
                data: {
                    id: id
                },
                success: function(response) {
                    $("#event_type_image").attr('src', response.image_path);
                    $('#image-Model').modal('show');
                }
            });
        });

        // Edit Button Click -> Fetch Data
        /* $(document).on('click', '.editIcon', function(e) {
            e.preventDefault();
            let id = $(this).attr('data-id');
            $.ajax({
                url: "{{ route('event_type.edit', '') }}/" + id,
                method: 'GET',
                success: function(response) {
                    $("#event_type_id").val(response.id);
                    $("#edit_event_type").val(response.event_type);
                    if (response.img_path) {
                        // Ensure path works correctly (no double slashes)
                        let imgUrl = response.img_path.startsWith('/') ? response.img_path : '/' + response.img_path;
                        $("#edit_image_preview").attr("src", imgUrl).show();
                    } else {
                        $("#edit_image_preview").hide();
                    }

                    $('#editEventTypeModel').modal('show');
                    console.log(response);
                },
                error: function(err) {
                    console.error("Edit fetch error:", err.responseText);
                    Swal.fire('Error!', 'Failed to fetch event type details.', 'error');
                }
            });
        });*/

        // Edit Button Click -> Fetch Data
        $(document).on('click', '.editIcon', function(e) {
            e.preventDefault();

            let id = $(this).attr('data-id'); // get the ID of the event type

            $.ajax({
                url: "{{ route('event_type.edit', '') }}/" + id, // Laravel route
                method: 'GET',
                success: function(response) {
                    // Fill the form fields
                    $("#event_type_id").val(response.id);
                    $("#edit_event_type").val(response.event_type);

                    // Handle image preview
                    if (response.img_path) {
                        $("#edit_image_preview")
                            .attr("src", response.img_path) // use full URL from controller
                            .show();
                    } else {
                        $("#edit_image_preview")
                            .attr("src", "") // remove previous image
                            .hide();
                    }

                    // Show the modal
                    $('#editEventTypeModel').modal('show');

                    // Optional: log response for debugging
                    console.log(response);
                },
                error: function(err) {
                    console.error("Edit fetch error:", err.responseText);
                    Swal.fire('Error!', 'Failed to fetch event type details.', 'error');
                }
            });
        });


        // ✅ Update Form Submit
        $("#edit_event_type_form").submit(function(e) {
            e.preventDefault();
            let formData = new FormData(this);

            $.ajax({
                url: "{{ route('event_type.update') }}",
                method: "POST", // if using resource route -> change to PUT + include ID
                data: formData,
                processData: false,
                contentType: false,
                success: function(res) {
                    if (res.success) { // ✅ fixed
                        $('#editEventTypeModel').modal('hide');
                        $("#edit_event_type_form")[0].reset();
                        Swal.fire({
                            icon: 'success',
                            title: res.title,
                            text: res.message
                        });
                        fetchAllEventTypes(); // refresh table
                    } else {
                        Swal.fire(res.title, res.message, res.type);
                    }
                },
                error: function(err) {
                    console.log(err.responseText);
                    Swal.fire('Error!', 'Something went wrong.', 'error');
                }
            });
        });

        // delete event type ajax request
        $(document).on('click', '.deleteIcon', function(e) {
            e.preventDefault();
            let id = $(this).attr('id');
            let csrf = '{{ csrf_token() }}';
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                confirmButtonText: 'Delete',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ route('event_type.delete') }}",
                        method: 'delete',
                        data: {
                            id: id,
                            _token: csrf
                        },
                        success: function(response) {
                            if (response.success) {
                                Swal.fire(
                                    'Deleted!',
                                    'Your file has been deleted.',
                                    'success'
                                )
                                fetchAllEventTypes();
                            } else {
                                Swal.fire(
                                    response.title,
                                    response.message,
                                    response.type
                                )
                            }
                        },
                        error: function(jqXHR, textStatus, errorThrown) {
                            Swal.fire(
                                'Error!',
                                textStatus.message,
                                'error'
                            )
                        }
                    });
                }
            })
        });

    });
</script>