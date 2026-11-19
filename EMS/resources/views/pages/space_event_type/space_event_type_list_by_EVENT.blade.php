@extends('layouts.app')

@section('title', 'Create New Events')

@section('content')
<style>
    .table {
        width: 100%;
        margin: 0;
    }

    .page-container {
        width: 100vw;
        /* full viewport width */
        margin: 0;
        padding: 0;
    }

    .card,
    .card-body {
        border: none !important;
        box-shadow: none !important;
    }

    /* Navbar height compensation */
    body {
        padding-top: px;
        /* ✅ Adjust if navbar height changes */
    }

    thead {
        position: sticky;
        top: 0;
        z-index: 10;
        background-color: #fff;
    }

    #sticky-header {
        position: sticky;
        top: 0;
        background-color: rgb(234, 243, 250);
        z-index: 10;
    }

    .create-new-class {
        background-color: #190575;
        color: white;
        border: none;
        padding: 6px 12px;
        border-radius: 4px;
        cursor: pointer;
    }

    .create-new-class:hover {
        background-color: #04043d;
    }

    .dropdown {
        position: relative;
        display: inline-block;

    }

    .dropdown-btn {
        background-color: #0a8028ff;
        color: white;
        padding: 8px 12px;
        font-size: 16px;
        border: none;
        cursor: pointer;
        width: 100%;
        text-align: left;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .dropdown-content {
        display: none;
        position: absolute;
        background-color: white;
        min-width: 220px;
        max-height: 200px;
        /* limits height */
        overflow-y: auto;
        /* scroll if content exceeds max height */
        box-shadow: 0px 8px 16px 0px rgba(0, 0, 0, 0.2);
        z-index: 1;
        margin-top: 4px;
        border-radius: 4px;
        padding: 5px 0;
    }

    .dropdown:hover .dropdown-content {
        display: block;
    }

    .dropdown-content a {
        color: black;
        padding: 8px 12px;
        text-decoration: none;
        display: block;
    }

    .dropdown-content a:hover {
        background-color: #f1f1f1;
    }

    .delete-btn {
        border: none;
        color: white;
        background: red;
        padding: 2px 6px;
        font-weight: bold;
        border-radius: 3px;
        cursor: pointer;
    }
</style>

<div class="page-container">
    <div class="container p-3 mb-3">
        <x-title>Manage Events</x-title>

        <div class="container-fluid mt-2 bg-white p-0">

            <div class="card border-0 shadow-none">
                <div class="card-body p-0">
                    <table class="table table-striped" id="eventsTable">
                        <thead id="sticky-header">
                            <tr>
                                <th class="text-left">Event</th>
                                <th class="text-center">Create Event</th>
                                <th class="text-center">Running Event</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($events as $event)
                            @php
                            $runningCount = $event->event_types
                            ->where('event_type_id', $event->id)
                            ->where('active_status', 'running')
                            ->count();

                            $completedCount = $event->event_types
                            ->where('event_type_id', $event->id)
                            ->where('active_status', 'completed')
                            ->count();

                            $runningStatCheck = $runningCount > 0;
                            $completedStatCheck = $completedCount == 0;
                            @endphp

                            <tr valign="middle">
                                <td class="text-left" style="border: 0; padding: 10px;">
                                    {{ $event->event_type }}
                                </td>

                                <td class="text-center" style="border: 0; padding: 10px;">
                                    <form action="{{ route('space_event.create') }}" method="GET">
                                        @csrf
                                        <input type="hidden" name="event_id" value="{{ $event->id }}">
                                        <button type="submit" class="create-new-class">
                                            Create New
                                        </button>
                                    </form>
                                </td>

                                <td class="text-center" style="border: 0; padding: 10px;">
                                    @if ($event->event_types->isEmpty() || !$runningStatCheck)
                                    <div style="width: 30vh; margin: auto;">No running events</div>
                                    @else
                                    <div class="dropdown">
                                        <button class="dropdown-btn">
                                            Show Events
                                            <i class='bx bx-chevron-down' style="margin-left:5px"></i>
                                        </button>
                                        <div class="dropdown-content">
                                            @foreach ($event->event_types->where('active_status', 'running') as $event_instance)
                                            <div style="display: flex; justify-content: space-between; align-items: center; padding: 4px 10px;">
                                                <a href="{{ route('space_event.runnigEvents', ['newEventId' => $event_instance->id]) }}">
                                                    {{ $event_instance->event_name }}
                                                </a>
                                                <button class="delete-btn" data-id="{{ $event_instance->id }}" title="Delete Event">&times;</button>
                                            </div>
                                            @endforeach
                                        </div>
                                    </div>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            @include('jQuery.event_type_jQ')
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        $('.view-events-btn').click(function() {
            var eventId = $(this).data('event-id');
            $.ajax({
                url: '/space_events/' + eventId,
                type: 'GET',
                success: function(response) {
                    // handle response
                },
                error: function(xhr) {
                    console.log(xhr.responseText);
                }
            });
        });
    });
</script>
@endsection