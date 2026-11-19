<script>
    $(function() {

        //add new event_type ajax request
        $("#new_event_type_form").submit(function(e) {
            e.preventDefault();
            const fd = new FormData(this);

            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: "{{ route('event_type.store') }}",
                method: 'post',
                data: fd,
                cache: false,
                contentType: false,
                processData: false,
                dataType: 'json',
                timeout: 10000, // Timeout 10 seconds
                success: function(response) {
                    $("#save").text('Adding...');

                    if (response.success) {
                        Swal.fire(
                            response.title,
                            response.message,
                            response.type
                        ).then((result) => {
                            if (result.isConfirmed) {
                                Swal.fire({
                                    title: 'Do you want to add the task?',
                                    text: "Please confirm if you want to proceed.",
                                    icon: 'question',
                                    showCancelButton: true,
                                    confirmButtonColor: '#0D6EFD',
                                    cancelButtonColor: '#d33',
                                    confirmButtonText: 'Yes',
                                    cancelButtonText: 'No',
                                    reverseButtons: true
                                }).then((result2) => {
                                    if (result2.isConfirmed) {
                                        let id = response.new_record
                                        $('#addTasksModal').data(
                                            'record-id', id).modal(
                                            'show');
                                    }
                                });
                            }
                        });
                    } else {
                        Swal.fire(
                            response.title,
                            response.message,
                            response.type
                        );
                    }
                    $("#save").text('Add');
                    $("#new_event_type_form")[0].reset();
                },
                error: function(xhr, status, error) {
                    console.log('AJAX Status:', status); // e.g., "error" or "timeout"
                    console.log('HTTP Error:', xhr.status); // e.g., 500
                    console.log('Error Thrown:', error); // e.g., "Internal Server Error"
                    console.log('Response Text:', xhr.responseText); // <-- Shows actual Laravel error message / stack

                    Swal.fire(
                        'Error!',
                        'There was an issue saving the data. Check the console for details.',
                        'error'
                    );
                }

            });
        });

        //add tasks to the newly created event
        $("#addTasksForm").submit(function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            formData.append(
                'event_id',
                $('#addTasksModal').data('record-id')
            ); //add event_id to the formData before sending it to the server
            $('#save').text('Adding...');

            $.ajax({
                type: "POST",
                url: "{{ route('task.store') }}",
                data: formData,
                dataType: "json",
                cache: false,
                contentType: false,
                processData: false,
                success: function(response) {
                    if (response.success) {
                        $('#addTasksForm')[0].reset(); // clear the form
                        $('#addTasksModal').modal('hide');
                        $('#save').text('Add');
                        Swal.fire(
                            response.title,
                            response.message,
                            response.type
                        ).then(() => {
                            Swal.fire({
                                title: 'Do you want to add another task?',
                                text: "Please confirm if you want to proceed.",
                                icon: 'question',
                                showCancelButton: true,
                                confirmButtonColor: '#0D6EFD',
                                cancelButtonColor: '#d33',
                                confirmButtonText: 'Yes',
                                cancelButtonText: 'No',
                                reverseButtons: true
                            }).then((result2) => {
                                if (result2.isConfirmed) {
                                    $('#addTasksModal').modal('show');
                                }
                            });
                        });
                    } else {
                        $('#save').text('Add');
                        Swal.fire(
                            response.title,
                            response.message,
                            response.type
                        );
                    }
                }

            });
        });

        // Handle the confirm cancellation button click in Bootstrap modal
        $(document).on('click', '#confirmCancel', function() {
            $("#new_event_type_form")[0].reset();
            var cancelModal = bootstrap.Modal.getInstance(document.getElementById('myModal'));
            cancelModal.hide();
        });

        //error message for the error function
        function errorMessage() {
            Swal.fire(
                'Error!',
                'There was an issue saving the data.',
                'error'
            );
        }
    });
</script>