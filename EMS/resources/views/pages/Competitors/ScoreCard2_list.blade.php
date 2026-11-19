@extends('layouts.app')

@push('css')
<style>
    /* Card Styles */
    .winner-card {
        width: 300px;
        height: 150px;
        font-size: 16px;
        padding: 10px 15px;
        border-radius: 12px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        color: #fff;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        position: relative;
    }

    .position-top-right {
        position: absolute;
        top: 10px;
        right: 15px;
        font-size: 32px;
        font-weight: bold;
        color: #000;
    }

    .card-content {
        text-align: left;
        margin-top: 10px;
    }

    .card-content h5,
    .card-content p {
        margin: 3px 0;
        font-size: 1rem;
    }

    .first-place .position-top-right {
        color: #ffed8a;
    }

    .second-place .position-top-right {
        color: #7b7b7b;
    }

    .third-place .position-top-right {
        color: #ffa74f;
    }

    .first-place {
        background-color: #ffc107;
        color: #212529;
    }

    .second-place {
        background-color: #b2b2b2;
        color: #212529;
        border: 1px solid #dee2e6;
    }

    .third-place {
        background-color: #b45f06;
        color: #fff;
    }

    .winner-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 6px 15px rgba(0, 0, 0, 0.2);
    }

    .top-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }

    .year-filter select {
        width: 120px;
    }
</style>
@endpush

@section('content')
<div class="container my-4">

    <!-- Top Row -->
    <div class="top-row px-3">
        <div>
            <a href="{{ route('artcompetitors.index') }}" class="btn btn-secondary">
                <i class='bx bx-arrow-back'></i> Back
            </a>
        </div>

        <div class="year-filter">
            <select id="yearFilter" class="form-select form-select-sm">
                <option value="">Year</option>
                @for ($y = 2025; $y <= date('Y') + 5; $y++)
                    <option value="{{ $y }}" {{ $y == $defaultYear ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
            </select>
        </div>
    </div>

    <!-- Header -->
    <div class="text-center mb-4" style="font-family:'Times New Roman', Times, serif;">
        <h1 style="font-size: 36px; color: #000279; font-weight: bold;">Art Competition</h1>
        <h2 style="font-size: 30px; color: #000279; font-weight: bold;">Score Card</h2>
    </div>

    <!-- Winner Cards -->
    <div class="row text-center mb-4">
        <!-- 1st Place -->
        <div class="col-md-4 mb-3">
            <div class="winner-card first-place">
                <div class="position-top-right">1st</div>
                <div class="card-content" id="firstPlaceCard">
                    <h5 class="fw-bold">Name:</h5>
                    <p>{{ $firstPlace->Name ?? '---' }}</p>
                    <p><strong>Prize Amount:</strong> {{ $firstPlace->Prize ?? '---' }}</p>
                </div>
            </div>
        </div>

        <!-- 2nd Place -->
        <div class="col-md-4 mb-3">
            <div class="winner-card second-place">
                <div class="position-top-right">2nd</div>
                <div class="card-content" id="secondPlaceCard">
                    <h5 class="fw-bold">Name:</h5>
                    <p>{{ $secondPlace->Name ?? '---' }}</p>
                    <p><strong>Prize Amount:</strong> {{ $secondPlace->Prize ?? '---' }}</p>
                </div>
            </div>
        </div>

        <!-- 3rd Place -->
        <div class="col-md-4 mb-3">
            <div class="winner-card third-place">
                <div class="position-top-right">3rd</div>
                <div class="card-content" id="thirdPlaceCard">
                    <h5 class="fw-bold">Name:</h5>
                    <p>{{ $thirdPlace->Name ?? '---' }}</p>
                    <p><strong>Prize Amount:</strong> {{ $thirdPlace->Prize ?? '---' }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Participants Table -->
    <div class="card border-0 shadow-lg p-3" style="max-height: 80vh; overflow-y: auto;">
        <div class="table-responsive">
            <table class="table table-bordered text-center" id="participantsTable">
                <thead class="table-secondary">
                    <tr>
                        <th>Name</th>
                        <th>Place</th>
                        <th>Prize</th>
                        <th>Incharge</th>
                        <th>Year</th>
                        <th>Certificate Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($participants as $participant)
                    <tr>
                        <td>{{ $participant->Name }}</td>
                        <td>{{ $participant->Place }}</td>
                        <td>{{ $participant->Prize }}</td>
                        <td>{{ $participant->Incharge }}</td>
                        <td>{{ $participant->Year }}</td>
                        <td>{{ $participant->certificate_status }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection

@push('js')
<script>
    $(document).ready(function() {
        $('#yearFilter').change(function() {
            let year = $(this).val();

            $.ajax({
                url: "{{ route('scorecard2.filter') }}",
                type: 'GET',
                data: {
                    year: year
                },
                success: function(data) {
                    // Update winner cards
                    $('#firstPlaceCard').html(`
                    <h5 class="fw-bold">Name:</h5>
                    <p>${data.firstPlace ? data.firstPlace.Name : '---'}</p>
                    <p><strong>Prize Amount:</strong> ${data.firstPlace ? data.firstPlace.Prize : '---'}</p>
                `);

                    $('#secondPlaceCard').html(`
                    <h5 class="fw-bold">Name:</h5>
                    <p>${data.secondPlace ? data.secondPlace.Name : '---'}</p>
                    <p><strong>Prize Amount:</strong> ${data.secondPlace ? data.secondPlace.Prize : '---'}</p>
                `);

                    $('#thirdPlaceCard').html(`
                    <h5 class="fw-bold">Name:</h5>
                    <p>${data.thirdPlace ? data.thirdPlace.Name : '---'}</p>
                    <p><strong>Prize Amount:</strong> ${data.thirdPlace ? data.thirdPlace.Prize : '---'}</p>
                `);

                    // Update table
                    let tbody = '';
                    data.participants.forEach(function(p) {
                        tbody += `<tr>
                        <td>${p.Name}</td>
                        <td>${p.Place}</td>
                        <td>${p.Prize}</td>
                        <td>${p.Incharge}</td>
                        <td>${p.Year}</td>
                        <td>${p.certificate_status}</td>
                    </tr>`;
                    });
                    $('#participantsTable tbody').html(tbody);
                }
            });
        });
    });
</script>
@endpush