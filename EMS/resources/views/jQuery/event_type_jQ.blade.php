<script>
    $("#eventsTable").DataTable({
        order: [
            [0, 'asc']
        ], // Default ordering on the "Previous Event" column
        scrollY: '52vh',
        scrollCollapse: true,
        paging: false,
        info: false,
        searching: false,
        lengthChange: false,
        columnDefs: [{
                orderable: true, // Enable ordering only for the "Previous Event" column
                targets: 0 // Assuming "Previous Event" is the second column (index 0)
            },
            {
                orderable: false, // Disable ordering for all other columns
                targets: '_all' // Apply to all other columns
            }
        ]
    });

    $(document).on('click', '.delete-btn', function() {
        let id = $(this).data('id');

        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "{{ url('space_events/delete') }}/" + id,

                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Deleted!',
                                text: response.message,
                                timer: 1000,
                                showConfirmButton: false
                            });

                            // Reload the page after 1.5s
                            setTimeout(function() {
                                location.reload();
                            }, 1500);
                        }
                    },
                    error: function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: 'Something went wrong!',
                        });
                    }
                });
            }
        });
    });

    $(document).on('click', '.update-btn', function() {
        let id = $(this).data('id');

        Swal.fire({
            title: 'Are you sure?',
            text: "You are about to update this event!",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, update it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `/space_events/update/${id}`,
                    type: 'POST', // Use POST or PUT depending on your route
                    data: {
                        _token: '{{ csrf_token() }}',
                        // Add any data you want to update here
                        // For example:
                        name: $(`#eventName-${id}`).val(),
                        date: $(`#eventDate-${id}`).val(),
                    },
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Updated!',
                                text: response.message,
                                timer: 1000,
                                showConfirmButton: false
                            });

                            // Reload the page after 1.5s
                            setTimeout(function() {
                                location.reload();
                            }, 1500);
                        }
                    },
                    error: function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: 'Something went wrong!',
                        });
                    }
                });
            }
        });
    });
</script>