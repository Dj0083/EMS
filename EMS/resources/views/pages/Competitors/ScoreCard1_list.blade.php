@extends('layouts.app')

@push('css')
<style>
    /* Card Styles (same as before) */
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

    .best-shooter-card {
        background-color: #2196f3;
        color: #fff;
        padding: 15px 20px;
        border-radius: 12px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        margin-bottom: 20px;
    }

    .best-shooter-card:hover {
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

    .best-shooter-card .row>div {
        margin-right: 25px;
        /* adjust spacing */
    }

    .best-shooter-card .row>div:last-child {
        margin-right: 0;
        /* remove extra space at the end */
    }
</style>
@endpush

@section('content')
<div class="container my-4">

    <!-- Top Row: Back + Year Filter -->
    <div class="top-row px-3">
        <div>
            <a href="{{ route('student.index') }}" class="btn btn-secondary"><i class='bx bx-arrow-back'></i> Back</a>
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
        <h1 style="font-size:36px; color:#000279; font-weight:bold;">National Water Rocket Competition</h1>
        <h2 style="font-size:30px; color:#000279; font-weight:bold;">Score Card</h2>
    </div>

    <!-- Winner Cards -->
    <div class="row text-center mb-4">
        <div class="col-md-4 mb-3">
            <div class="winner-card first-place" id="firstPlaceCard">
                <div class="position-top-right">1st</div>
                <div class="card-content">
                    <h5 class="fw-bold">Name:</h5>
                    <p>{{ $firstPlace->Name ?? '---' }}</p>
                    <p><strong>Avg:</strong> {{ isset($firstPlace->average) ? number_format($firstPlace->average,3) : '---' }}</p>
                    <p><strong>Prize Amount:</strong> {{ $firstPlace->prize ?? '---' }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="winner-card second-place" id="secondPlaceCard">
                <div class="position-top-right">2nd</div>
                <div class="card-content">
                    <h5 class="fw-bold">Name:</h5>
                    <p>{{ $secondPlace->Name ?? '---' }}</p>
                    <p><strong>Avg:</strong> {{ isset($secondPlace->average) ? number_format($secondPlace->average,3) : '---' }}</p>
                    <p><strong>Prize Amount:</strong> {{ $secondPlace->prize ?? '---' }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="winner-card third-place" id="thirdPlaceCard">
                <div class="position-top-right">3rd</div>
                <div class="card-content">
                    <h5 class="fw-bold">Name:</h5>
                    <p>{{ $thirdPlace->Name ?? '---' }}</p>
                    <p><strong>Avg:</strong> {{ isset($thirdPlace->average) ? number_format($thirdPlace->average,3) : '---' }}</p>
                    <p><strong>Prize Amount:</strong> {{ $thirdPlace->prize ?? '---' }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Best Shooter -->
    <!-- Best Shooter -->
    <div class="best-shooter-card" id="bestShooterCard">
        <div class="row text-center text-md-start align-items-center d-flex flex-wrap gap-4">
            <div class="col-md-auto">
                <h5 class="fw-bold">Best Shooter</h5>
            </div>
            <div class="col-md-auto"><strong>Name:</strong> {{ $bestShooter->Name ?? '---' }}</div>
            <div class="col-md-auto"><strong>Atm 1:</strong> {{ isset($bestShooter->Attempt_1) ? number_format($bestShooter->Attempt_1, 3) : '---' }}</div>
            <div class="col-md-auto"><strong>Atm 2:</strong> {{ isset($bestShooter->Attempt_2) ? number_format($bestShooter->Attempt_2, 3) : '---' }}</div>
            <div class="col-md-auto"><strong>Avg:</strong> {{ isset($bestShooter->average) ? number_format($bestShooter->average, 3) : '---' }}</div>
        </div>
    </div>




    <!-- Participants Table -->
    <div class="table-responsive">
        <table class="table table-bordered text-center" id="participantsTable">
            <thead class="table-secondary">
                <tr>
                    <th>Name</th>
                    <th>Atm 1(m)</th>
                    <th>Atm 2(m)</th>
                    <th>Avg(m)</th>
                    <th>Place</th>
                    <th>Prize</th>
                    <th>Year</th>
                    <th>Certificate Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($participants as $p)
                <tr>
                    <td>{{ $p->Name }}</td>
                    <td>{{ $p->Attempt_1 }}</td>
                    <td>{{ $p->Attempt_2 }}</td>
                    <td>{{ isset($p->average) ? number_format($p->average,3) : '---' }}</td>
                    <td>{{ $p->place ?? '---' }}</td>
                    <td>{{ $p->prize ?? '---' }}</td>
                    <td>{{ $p->Year }}</td>
                    <td>{{ $p->certificate_status ?? '---' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

</div>
@endsection

@push('js')
<script>
    $(document).ready(function() {

        function formatNumber(value) {
            return (value !== null && value !== undefined) ? Number(value).toFixed(3) : '---';
        }

        function updateWinnerCards(data) {
            $('#firstPlaceCard').html(`
            <div class="position-top-right">1st</div>
            <div class="card-content">
                <h5 class="fw-bold">Name:</h5>
                <p>${data.firstPlace?.Name ?? '---'}</p>
                <p><strong>Avg:</strong> ${formatNumber(data.firstPlace?.average)}</p>
                <p><strong>Prize Amount:</strong> ${data.firstPlace?.prize ?? '---'}</p>
            </div>
        `);
            $('#secondPlaceCard').html(`
            <div class="position-top-right">2nd</div>
            <div class="card-content">
                <h5 class="fw-bold">Name:</h5>
                <p>${data.secondPlace?.Name ?? '---'}</p>
                <p><strong>Avg:</strong> ${formatNumber(data.secondPlace?.average)}</p>
                <p><strong>Prize Amount:</strong> ${data.secondPlace?.prize ?? '---'}</p>
            </div>
        `);
            $('#thirdPlaceCard').html(`
            <div class="position-top-right">3rd</div>
            <div class="card-content">
                <h5 class="fw-bold">Name:</h5>
                <p>${data.thirdPlace?.Name ?? '---'}</p>
                <p><strong>Avg:</strong> ${formatNumber(data.thirdPlace?.average)}</p>
                <p><strong>Prize Amount:</strong> ${data.thirdPlace?.prize ?? '---'}</p>
            </div>
        `);
        }

        function updateBestShooterCard(data) {
            $('#bestShooterCard').html(`
        <div class="row text-center text-md-start">
            <div class="col-md-3"><h5 class="fw-bold">Best Shooter</h5></div>
            <div class="col-md-2"><strong>Name:</strong> ${data.bestShooter?.Name || '---'}</div>
             <div class="col-md-2"><strong>Prize Amount:</strong> ${data.bestShooter?.prize ?? '---'}</div>
            <div class="col-md-1"><strong>Attempt 1:</strong> ${formatNumber(data.bestShooter?.Attempt_1)}</div>
            <div class="col-md-1"><strong>Attempt 2:</strong> ${formatNumber(data.bestShooter?.Attempt_2)}</div>
            <div class="col-md-2"><strong>Avg:</strong> ${formatNumber(data.bestShooter?.average)}</div>
        </div>
    `);
        }


        function updateParticipantsTable(participants) {
            let tbody = '';
            if (participants.length === 0) {
                tbody = `<tr><td colspan="8">No participants found for this year.</td></tr>`;
            } else {
                participants.forEach(p => {
                    tbody += `<tr>
                    <td>${p.Name}</td>
                    <td>${p.Attempt_1}</td>
                    <td>${p.Attempt_2}</td>
                    <td>${formatNumber(p.average)}</td>
                    <td>${p.place ?? '---'}</td>
                    <td>${p.prize ?? '---'}</td>
                    <td>${p.Year}</td>
                    <td>${p.certificate_status ?? '---'}</td>
                </tr>`;
                });
            }
            $('#participantsTable tbody').html(tbody);
        }

        $('#yearFilter').change(function() {
            let year = $(this).val();
            $.ajax({
                url: "{{ route('scorecard1.filter') }}",
                type: 'GET',
                data: {
                    year: year
                },
                success: function(data) {
                    updateWinnerCards(data);
                    updateBestShooterCard(data);
                    updateParticipantsTable(data.participants);
                },
                error: function(xhr, status, error) {
                    console.error(error);
                    alert('Failed to fetch data for the selected year.');
                }
            });
        });

    });
</script>
@endpush