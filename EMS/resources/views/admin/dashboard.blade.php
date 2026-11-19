@extends('layouts.app')

@section('title', 'Dashboard')

@push('css')
<link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
<style>
    .scroll {
        display: grid;
        grid-template-columns: repeat(4, minmax(200px, 1fr));
        /* 4 cards per row, auto-shrink */
        gap: 20px;
    }

    .card-hover {
        flex: 1 1 300px;
        /* Responsive width */
        width: 100%;
    }

    .cards-container {
        opacity: 0;
        transform: translateY(40px);
        animation: fadeInCards 1s ease-in-out forwards;
        animation-delay: 1.8s;
    }

    @keyframes fadeInCards {
        from {
            opacity: 0;
            transform: translateY(40px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
</style>
@endpush

@section('content')
<div class="container content-wrapper">
    <div class="intro text-center">
        <x-title>
            Event Management System of <br>The Space Astronomy Division
        </x-title>

        <h3>
            The system streamlines event organization effortlessly.
            Upcoming events and associated tasks are presented with ease, facilitating coordination.
        </h3>
    </div>

    <div class="cards-container mt-4">
        <div class="scroll">
            @foreach ($new_event as $event)
            <div class="card-hover">
                @if(isset($event->eventType->img_path) && $event->eventType->img_path)
                <x-cardimage
                    imageUrl="{{ asset($event->eventType->img_path) }}"
                    imageAlt="{{ $event->event_name }}"
                    title="{{ $event->event_name }}"
                    content="{{ $event->description }}"
                    sdate="{{ $event->start_date }}"
                    edate="{{ $event->end_date }}"
                    eid="{{ $event->id }}" />
                @endif
            </div>
            @endforeach
        </div>
    </div>
</div>
@endsection