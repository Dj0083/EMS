@extends('layouts.app')

@section('title')
Student List
@endsection

@push('css')
<style>
    .container {
        overflow-y: auto;
    }
</style>
@endpush

@section('content')
<x-title>Water Rocket Competitors.</x-title>

{{-- Top Left Buttons --}}
<div class="mb-3 d-flex justify-content-start gap-2 px-3">
    <a href="{{ route('prizes.index') }}" class="btn btn-success">
        <i class='bx bx-gift'></i> Prizes
    </a>
    <a href="{{ route('scorecard1.index') }}" class="btn btn-primary">
        <i class='bx bx-show'></i> View Scoreboard
    </a>
    <a href="{{ route('artcomcriteria.index') }}" class="btn btn-primary">
        <i class='bx bx-list-plus'></i> Add Criteria
    </a>
</div>



<div class="container-fluid p-3 mb-3" style="min-height: 80vh;">
    <div class="row">

        {{-- Table --}}
        <div class="col-12 col-md-7 order-last order-md-first ">
            <div style="height: 600px;">
                <div class="card border-0 shadow-lg" style="max-height: 100vh; overflow-y: auto;">
                    <div class="card-body" id="student">
                        {{-- Student table goes here --}}
                    </div>
                </div>
            </div>
        </div>

        {{-- Form --}}
        <div class="col-12 col-md-5 mt-4 order-first order-md-last mb-4 mb-md-0">
            <div>

                <div class="card border-0">
                    <form id="new_student_adding"
                        action="{{ route('student.store') }}"
                        method="POST"
                        enctype="multipart/form-data">
                        @csrf
                        <div class="card border-0 shadow-lg p-3">
                            <div class="card-header">
                                <h5 class="card-title">
                                    <i class='bx bx-add-to-queue'></i>
                                    Add New Competitor
                                </h5>
                            </div>
                            <div class="card-body">

                                <div class="mb-3">
                                    <label for="com_id" class="form-label">Competitor ID</label>
                                    <input type="number" min="0" name="com_id" id="com_id" class="form-control" required>
                                </div>

                                <div class="mb-3">
                                    <label for="com_name" class="form-label">Name</label>
                                    <input type="text" name="com_name" id="com_name" class="form-control" required>
                                </div>

                                <div class="mb-3">
                                    <label for="com_school" class="form-label">School</label>
                                    <input type="text" name="com_school" id="com_school" class="form-control" required>
                                </div>

                                <div class="mb-3">
                                    <label for="com_incharge" class="form-label">Incharge</label>
                                    <input type="text" name="com_incharge" id="com_incharge" class="form-control" required>
                                </div>

                                <div class="mb-3">
                                    <label for="com_att1" class="form-label">Attempt 1</label>
                                    <input type="number" step="0.01" name="com_attempt_1" id="com_att1" class="form-control" required>
                                </div>

                                <div class="mb-3">
                                    <label for="com_att2" class="form-label">Attempt 2</label>
                                    <input type="number" step="0.01" name="com_attempt_2" id="com_att2" class="form-control" required>
                                </div>

                                <!-- <div class="mb-3"> 
                                    <label for="com_avg" class="form-label">Average</label>
                                    <input type="text" name="Average" id="com_avg" class="form-control" placeholder="0.00" readonly>
                                </div>-->

                                <div class="mb-3">
                                    <label for="com_email" class="form-label">Email</label>
                                    <input type="email" name="com_email" id="com_email" class="form-control" required>
                                </div>

                                <div class="mb-3">
                                    <label for="com_contact" class="form-label">Contact</label>
                                    <input type="tel" name="com_contact" id="com_contact" class="form-control" required>
                                </div>

                                <div class="mb-3">
                                    <label for="com_year" class="form-label">Year</label>
                                    <input type="number" min="2025" name="com_year" id="com_year" class="form-control" required>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label d-block">Certificate Status:</label>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="certificate_status" id="cert_issued" value="issued">
                                        <label class="form-check-label" for="cert_issued">Issued</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="certificate_status" id="cert_not_issued" value="not_issued" checked>
                                        <label class="form-check-label" for="cert_not_issued">Not Issued</label>
                                    </div>
                                </div>

                                <x-submit-btn class="mt-3 float-end" id="add">Add</x-submit-btn>
                                <x-reset-btn class="mt-3 mx-2 float-end" />

                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Edit Competitor modal --}}
        <div class="modal fade" id="editCompetitorModel" tabindex="-1" aria-labelledby="exampleModalLabel"
            data-bs-backdrop="static" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Edit Competitor</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="#" method="post" id="edit_competitor_form" enctype="multipart/form-data">
                        @csrf
                        <div class="modal-body p-4 bg-light">
                            <div class="col-lg">
                                <label for="edit_cometitor_id">Competitor ID</label>
                                <input type="text" name="com_id" id="competitor_id" hidden="true">
                                <input type="text" name="edit_competitor_id" id="edit_competitor_id" class="form-control"
                                    placeholder="Enter Competitor no" required>
                            </div>
                            <div class="col-lg">
                                <label for="edit_competitor_name">Competitor Name</label>
                                <input type="text" name="edit_competitor_name" id="edit_competitor_name"
                                    class="form-control" placeholder="Enter competitor name" required>
                            </div>
                            <div class="col-lg">
                                <label for="edit_competitor_school">Competitor School</label>
                                <input type="text" name="edit_competitor_school" id="edit_competitor_school"
                                    class="form-control" placeholder="Enter competitor school" required>
                            </div>
                            <div class="col-lg">
                                <label for="edit_competitor_incharge">Competitor Incharge</label>
                                <input type="text" name="edit_competitor_incharge" id="edit_competitor_incharge"
                                    class="form-control" placeholder="Enter competitor incharge" required>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" id="edit_competitor_btn" class="btn btn-success">Update</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection

@push('js')
@include("jQuery.student_list_jQ")

<!-- <script> 
    const att1 = document.getElementById('com_att1');
    const att2 = document.getElementById('com_att2');
    const avg = document.getElementById('com_avg');

    function calculateAverage() {
        const val1 = parseFloat(att1.value) || 0;
        const val2 = parseFloat(att2.value) || 0;
        avg.value = ((val1 + val2) / 2).toFixed(2);
    }

    att1.addEventListener('input', calculateAverage);
    att2.addEventListener('input', calculateAverage);
</script>-->


@endpush