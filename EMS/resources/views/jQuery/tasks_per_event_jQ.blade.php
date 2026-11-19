<script type="text/javascript">
    const eventId = @json($eventId);

    $(function() {
        fetchTasks();

        /**
         * Fetches tasks related to a specific event and updates the tasks table.
         *
         * @param int $eventId The ID of the event for which tasks are to be fetched.
         *
         * @return void
         */
        function fetchTasks() {
            let url = "{{ route('task.fetchTasks', ':id') }}".replace(':id', eventId);

            $.ajax({
                url: url,
                type: 'GET',
                success: function(response) {
                    $("#tasks").html(response);
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    console.log(textStatus, errorThrown);
                    console.error(jqXHR.responseText);
                }
            });
        }

        //add task to the selected event
        $("#addTasksForm").submit(function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            formData.append('event_id', eventId); //add event_id to the formData before sending it to the server
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
                        Swal.fire(response.title, response.message, response.type);
                        fetchTasks();
                        $('#addTasksForm')[0].reset();
                        $('#addTasksModal').modal('hide');
                        $('#save').text('Add');
                    } else {
                        $('#save').text('Add');
                        Swal.fire(response.title, response.message, response.type);
                    }
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    console.error(textStatus, errorThrown);
                    console.error(jqXHR.responseText);
                }
            });
        });

        //show the task name edit modal
        $(document).on('click', '.editTask', function(e) {
            e.preventDefault();
            let id = $(this).attr('id');
            $.ajax({
                type: "GET",
                url: "{{ route('task.edit') }}",
                data: {
                    id: id
                },
                success: function(response) {
                    $('#editTaskNameModal').modal('show');
                    $('#task_id').val(response.id);
                    $('#event_type_id').val(response.event_type_id);
                    $('#taskName').val(response.task_name);
                }
            });
        });

        //submit the task edit form
        $('#update').on('click', function(e) {
            e.preventDefault();

            const formData = new FormData($('#editTaskNameForm')[0]);
            $.ajax({
                type: "POST",
                url: "{{ route('task.update') }}",
                data: formData,
                dataType: 'json',
                cache: false,
                contentType: false,
                processData: false,
                success: function(response) {
                    if (response.success) {
                        Swal.fire(response.title, response.message, response.type);
                        $('#editTaskNameForm')[0].reset();
                        $('#editTaskNameModal').modal('hide');
                        fetchTasks();
                    } else {
                        Swal.fire(response.title, response.message, response.type);
                    }
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    console.error(textStatus, errorThrown);
                    console.error(jqXHR.responseText);
                    Swal.fire('Error', 'An error occurred. Please try again.', 'error');
                }
            });
        });

        let addBtnId; //using to catch the task id of the selected button

        //show file input menu on button click
        $(document).on('click', '.addAttach', function() {
            $('#fileInput').click();
            addBtnId = $(this).attr('id');
        });

        //upload attachments
        $('#fileInput').on('change', function(event) {
            const files = event.target.files;
            const formData = new FormData();

            formData.append('taskId', addBtnId);
            formData.append('_token', '{{ csrf_token() }}');

            for (const file of files) {
                formData.append('attachments[]', file);
            }

            $.ajax({
                url: "{{ route('task.addAttachment') }}",
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.success) {
                        Swal.fire(response.title, response.message, response.type);
                        fetchTasks();
                    } else {
                        Swal.fire(response.title, response.message, response.type);
                    }
                },
                error: function(error) {
                    console.error('Upload failed:', error);
                }
            });
        });

        //delete task
        $(document).on('click', '.delTask', function() {
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
                        method: 'DELETE',
                        url: "{{ route('task.deleteTask') }}",
                        data: {
                            id: id,
                            _token: csrf
                        },
                        success: function(response) {
                            if (response.success) {
                                Swal.fire('Deleted!', 'Task deleted successfully', 'success');
                                fetchTasks();
                            } else {
                                Swal.fire("Unable to delete", "Please delete related attachments first.", "error");
                            }
                        },
                        error: function(error) {
                            console.error('Delete Failed:', error);
                        }
                    });
                }
            });
        });


        //delete attachments
        $(document).on('click', '.delAttach', function() {
            let attachId = $(this).data('id');
            const csrf = '{{ csrf_token() }}';

            Swal.fire({
                title: 'Do you Want to Delete This Attachment?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                confirmButtonText: 'Delete',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        type: "DELETE",
                        url: "{{ route('task.deleteAttachment') }}",
                        data: {
                            id: attachId,
                            _token: csrf
                        },
                        dataType: "json",
                        success: function(response) {
                            if (response.success) {
                                Swal.fire(response.title, response.message, response.type);
                                fetchTasks();
                            } else {
                                Swal.fire(response.title, response.message, response.type);
                            }
                        },
                        error: function(error) {
                            console.error('Delete Failed:', error);
                            Swal.fire('Not Found', 'File not found', 'error');
                        }
                    });
                }
            });
        });
    });
</script>