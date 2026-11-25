@extends('layouts.app')

@section('title')
Running Event
@endsection

@section('content')

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <style>
        .taskname {
            border: none !important;
            background: transparent !important;
            width: 100%;
        }

        .btn-warning {
            background-color: yellow;
            color: black;
        }

        .btn-success {
            background-color: green;
            color: white;
        }

        .container {
            display: flex;
            justify-content: space-between;
            gap: 10px;
            width: 100%;
        }

        .section1,
        .section2 {
            flex: 1;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 8px;
            background-color: #ffffff;
        }

        .section1 {
            max-width: 40%;
        }

        .section2 {
            max-width: 60%;
        }

        table {
            width: 100%;
        }
    </style>
</head>

<x-title class="main-title">
    Running Event Of
</x-title>

<x-title class="division-title">
    {{ $eventTypeName->event_type }}
</x-title>

<br />

<div class="container">
    <!-- Section 1: Event Organizing Details -->
    <div class="container p-3 mb-3" style="min-height: 80vh;">
        <div class="section1">
            <h4 class="text-center">Organizing Details</h4>

            <div class="d-flex justify-content-between mb-3">
                <a href="{{ route('space_event.index1') }}" class="btn btn-secondary btn-sm">
                    <i class="bi bi-arrow-left"></i> Back
                </a>

                <button type="button" class="btn btn-primary btn-sm editOrganizingBtn"
                    data-id="{{ $runnigEvent->id }}">
                    <i class="bi bi-database-add"></i> Edit
                </button>

            </div>
            <div class="card border-0 shadow-lg">
                <div class="card-body">
                    <table class="table table-striped">
                        <tbody>
                            <tr>
                                <th>Event Id</th>
                                <td>{{ $runnigEvent->id }}</td>
                            </tr>
                            <tr>
                                <th>Year</th>
                                <td>{{ \Carbon\Carbon::parse($runnigEvent->start_date)->format('Y') }}</td>
                            </tr>
                            <tr>
                                <th>Event Name</th>
                                <td>{{ $runnigEvent->event_name }}</td>
                            </tr>
                            <tr>
                                <th>Start Date</th>
                                <td>{{ $runnigEvent->start_date }}</td>
                            </tr>
                            <tr>
                                <th>End Date</th>
                                <td>{{ $runnigEvent->end_date }}</td>
                            </tr>
                            <tr>
                                <th>Coordinator</th>
                                <td>{{ $runnigEvent->emp_name }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>


        <!-- Section 2: Task List -->
        <div class="section2">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h4 class="text-center font-weight-bold mb-0">Task List</h4>
                @if ($availableTasks->isNotEmpty())
                <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addTasksModal">
                    Add Tasks
                </button>
                @endif
            </div>
            <p class="small text-muted">
                Check each employee when their part is complete. The task becomes Done when all assigned employees are checked.
                Uncheck an employee to return the task to To do.
            </p>

            @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            @if ($errors->any())
            <div class="alert alert-danger">{{ $errors->first() }}</div>
            @endif

            <div class="card border-0 shadow-lg">
                <div class="card-body">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Task</th>
                                <th>Assigned Employee(s)</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if (empty($eventinstancetasks))
                            <tr>
                                <td colspan="3" class="text-center text-muted">
                                    No tasks have been added to this event yet.
                                </td>
                            </tr>
                            @endif
                            @foreach ($eventinstancetasks as $eventinstancetask)
                            <tr>
                                <td>{{ $eventinstancetask['task_name'] }}</td>
                                <td>
                                    @php

                                    $taskId = $eventinstancetask['tasks_id'] ?? null;

                                    $employees = $taskId && isset($assignedEmployees[$taskId])
                                    ? $assignedEmployees[$taskId]
                                    : collect();

                                    @endphp

                                    @if (isset($employees))
                                    <ul class="mb-0 ps-3">
                                        @foreach ($employees as $emp)
                                        <li>
                                            {{ $emp->emp_name }}
                                            <input type="checkbox"
                                                onchange="updateStatus('{{ $eventinstancetask['id'] }}', '{{ $emp->id }}', this)"
                                                class="form-check-input me-1"
                                                id="chk_{{ $eventinstancetask['id'] }}_{{ $emp->id }}"
                                                @if (isset($assignedList[$eventinstancetask['id']][$emp->id]) && $assignedList[$eventinstancetask['id']][$emp->id]['emp_status'] == 'Done') checked @endif>
                                        </li>
                                        @endforeach
                                    </ul>
                                    @else
                                    <span class="text-muted">Not Assigned</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($eventinstancetask['status'] == 'Done')
                                    <span class="badge bg-success">Done</span>
                                    @else
                                    <span class="badge bg-warning text-dark">To do</span>
                                    @endif
                                </td>


                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    @if ($availableTasks->isNotEmpty())
    <div class="modal fade" id="addTasksModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <form method="POST" action="{{ route('space_event.tasks.store', $runnigEvent->id) }}">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Add Tasks to {{ $runnigEvent->event_name }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        @if ($errors->any())
                        <div class="alert alert-danger">
                            {{ $errors->first() }}
                        </div>
                        @endif
                        @foreach ($availableTasks as $availableTask)
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="tasks[]"
                                value="{{ $availableTask->id }}" id="add-task-{{ $availableTask->id }}">
                            <label class="form-check-label" for="add-task-{{ $availableTask->id }}">
                                {{ $availableTask->task_name }}
                            </label>
                        </div>
                        @endforeach
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Add Selected Tasks</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    @endif

    @if ($errors->any() && $availableTasks->isNotEmpty())
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            bootstrap.Modal.getOrCreateInstance(document.getElementById('addTasksModal')).show();
        });
    </script>
    @endif

    {{-- Edit Modal --}}
    <!-- Edit Modal -->
    <div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <form id="edit_form" method="POST" action="{{ route('running-event.update', $runnigEvent->id) }}">

                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Update Event</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body bg-light p-4">
                        <input type="hidden" id="editEventId" name="event_id">

                        <div class="mb-3">
                            <label>Event Name</label>
                            <input type="text" id="editEventName" class="form-control" readonly>
                        </div>

                        <div class="mb-3">
                            <label>Start Date</label>
                            <input type="date" name="sDate" id="editStartDate" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label>End Date</label>
                            <input type="date" name="eDate" id="editEndDate" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label>Coordinator</label>
                            <select name="coordinatorID" class="form-control" id="coordinatorID" required>
                                <option value="">-- Select Coordinator --</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button class="btn btn-success" type="submit">Update</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        function updateStatus(taskId, empId, checkbox) {
            const completed = checkbox.checked;

            Swal.fire({
                title: completed ? 'Mark as done?' : 'Move back to To do?',
                text: completed
                    ? 'This employee will be marked as finished.'
                    : 'This employee will no longer be marked as finished.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: completed ? 'Mark done' : 'Set to To do'
            }).then((result) => {
                if (!result.isConfirmed) {
                    checkbox.checked = !completed;
                    return;
                }

                checkbox.disabled = true;
                fetch("{{ url('/update-task-status') }}", {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            },
                            body: JSON.stringify({
                                event_instance_task_id: taskId,
                                assigned_emp_id: empId,
                                completed: completed
                            })
                        })
                        .then(response => {
                            if (!response.ok) throw new Error('Network response was not ok: ' + response.status);
                            return response.json();
                        })
                        .then(data => {
                            if (data.success) {
                                Swal.fire('Updated!', data.message, 'success')
                                    .then(() => location.reload());
                            } else {
                                Swal.fire('Oops!', data.message, 'error');
                                checkbox.checked = !completed;
                                checkbox.disabled = false;
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            Swal.fire('Oops!', 'Something went wrong.', 'error');
                            checkbox.checked = !completed;
                            checkbox.disabled = false;
                        });
            });
        }


        $('#edit_form').on('submit', function(e) {
            e.preventDefault();

            let formData = $(this).serialize();
            let eventId = $('#editEventId').val(); // get the event id from hidden input

            $.ajax({
                url: "{{ route('running-event.update', ':id') }}".replace(':id', $('#editEventId').val()),
                type: 'POST',
                data: formData,
                success: function(response) {
                    $('#editModal').modal('hide');
                    Swal.fire('Success!', 'Event updated successfully.', 'success')
                        .then(() => {
                            location.reload();
                        });
                },
                error: function(xhr) {
                    Swal.fire('Error!', 'Update failed. Please try again.', 'error');
                }
            });
        });




        $(document).on('click', '.editOrganizingBtn', function() {
            const eventId = $(this).data('id');

            $.ajax({
                // url: `/running-event/${eventId}/edit`,
                url: "{{ route('running-event.edit', ':id') }}".replace(':id', eventId),
                type: 'GET',
                success: function(response) {
                    $('#editEventName').val(response.event.event_name);
                    $('#editStartDate').val(response.event.start_date);
                    $('#editEndDate').val(response.event.end_date);
                    $('#editEventId').val(response.event.id);

                    var dropdown = $('#coordinatorID');
                    dropdown.empty();
                    dropdown.append('<option value="">-- Select Coordinator --</option>');

                    $.each(response.coordinators, function(key, coordinator) {
                        var selected = (coordinator.id == response.event.coordinator_id) ? 'selected' : '';
                        dropdown.append(`<option value="${coordinator.id}" ${selected}>${coordinator.emp_name}</option>`);
                    });

                    // Form action set
                    $('#edit_form').attr('action', `/running-event/${eventId}/update`);
                    $('#editModal').modal('show');
                },
                error: function() {
                    Swal.fire('Error', 'Failed to load data!', 'error');
                }
            });
        });
    </script>
    @endsection