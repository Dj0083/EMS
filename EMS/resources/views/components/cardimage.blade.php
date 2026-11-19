<div class="card h-100" style="display: flex; flex-direction: column;">
    <img src="{{ $imageUrl }}"
        alt="{{ $imageAlt }}"
        class="card-img-top"
        style="height: 180px; object-fit: cover;">

    <div class="card-body d-flex flex-column">
        <h5 class="card-title" style="font-size: 14px; font-weight: bold; color: #100C5E;">
            {{ $title }}
        </h5>
        <p class="card-text flex-grow-1">{{ $content }}</p>
        <p class="card-text">Start: {{ $sdate }} End: {{ $edate }}</p>
        <a href="{{ route('space_event.runnigEvents', ['newEventId' => $eid]) }}">
            <button style="padding: 5px 10px; background-color: #0AD1C8; color: white; border: none; border-radius: 4px; cursor: pointer;">
                See More Details...
            </button>
        </a>
    </div>
</div>