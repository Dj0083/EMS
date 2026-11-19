@extends('layouts.app')

@section('title')
Student List
@endsection

@push('css')
<style>
    .container {
        overflow-y: auto;
    }

    .view-img-icon {
        cursor: pointer;
        font-size: 1.3rem;
        color: #0d6efd;
    }

    .view-img-icon:hover {
        color: #0a58ca;
    }

    .img-modal-img {
        width: 100%;
        height: auto;
    }
</style>
@endpush

@section('content')
<x-title>Art Competition Competitors.</x-title>

{{-- Top Left Buttons --}}
<div class="mb-3 d-flex justify-content-start gap-2 px-3">
    <a href="{{ route('scorecard2.index') }}" class="btn btn-primary">
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
                        {{-- Competitor table goes here --}}
                    </div>
                </div>
            </div>
        </div>

        {{-- form --}}
        <div class="col-12 col-md-5 mt-4 order-first order-md-last mb-4 mb-md-0">
            <div>
                <div class="card border-0">
                    <form id="new_competitor_adding" action="{{ route('artcompetitors.store') }}" method="post" enctype="multipart/form-data">
                        @csrf
                        <div class="card border-0 shadow-lg p-3">
                            <div class="card-header">
                                <h5 class="card-title">
                                    <i class='bx bx-add-to-queue'></i>
                                    Add New Competitor.
                                </h5>
                            </div>
                            <div class="card-body">
                                {{-- === Keep this entire form exactly as it is === --}}
                                <div class="mb-3">
                                    <label for="arcom_id" class="form-label">Competitor ID</label>
                                    <input type="number" min="0" name="arcom_id" id="arcom_id" placeholder="Enter Competitor ID" class="form-control" required>
                                </div>
                                <div class="mb-3">
                                    <label for="arcom_name" class="form-label">Name</label>
                                    <input type="text" name="arcom_name" id="arom_name" placeholder="Enter Competitor Name" class="form-control" required>
                                </div>
                                <div class="mb-3">
                                    <label for="arcom_school" class="form-label">School</label>
                                    <input type="text" name="arcom_school" id="arcom_school" placeholder="Enter competitor school" class="form-control" required>
                                </div>
                                <div class="mb-3">
                                    <label for="arcom_incharge" class="form-label">Incharge</label>
                                    <input type="text" name="arcom_incharge" id="arcom_incharge" placeholder="Enter Incharge" class="form-control" required>
                                </div>
                                <div class="mb-3">
                                    <label for="arcom_place" class="form-label">Place</label>
                                    <input type="number" min="0" name="arcom_place" id="arcom_place" placeholder="Enter place" class="form-control" required>
                                </div>
                                <div class="mb-3">
                                    <label for="arcom_prize" class="form-label">Prize</label>
                                    <input type="text" name="arcom_prize" id="arcom_prize" placeholder="Enter Prize" class="form-control" required>
                                </div>
                                <div class="mb-3">
                                    <label for="art_file" class="form-label">Add Art</label>
                                    <input type="file" name="art_file" id="art_file" class="form-control" required>
                                </div>
                                <div class="mb-3">
                                    <label for="arcom_email" class="form-label">Email</label>
                                    <input type="email" name="arcom_email" id="arcom_email" placeholder="Enter Email" class="form-control" required>
                                </div>
                                <div class="mb-3">
                                    <label for="arcom_contact" class="form-label">Contact</label>
                                    <input type="tel" name="arcom_contact" id="arcom_contact" placeholder="Enter Contact" class="form-control" required>
                                </div>
                                <div class="mb-3">
                                    <label for="arcom_year" class="form-label">Year</label>
                                    <input type="number" min="2025" name="arcom_year" id="arcom_year" placeholder="Enter year" class="form-control" required>
                                </div>

                                {{-- Certificate Status --}}
                                <div class="mb-3">
                                    <label class="form-label d-block">Certificate Status:</label>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="certificate_status" id="cert_issued" value="issued" required>
                                        <label class="form-check-label" for="cert_issued">Issued</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="certificate_status" id="cert_not_issued" value="not_issued" required>
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
                                <input type="text" name="edit_competitor_id" id="edit_competitor_id" class="form-control" readonly
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

        {{-- Image Viewing Modal --}}
        <div class="modal fade" id="viewImageModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-body p-0">
                        <img src="" alt="Competitor Art" class="img-modal-img" id="viewImage">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection

@push('js')
@include("jQuery.ArtCompetitors_jQ")

@endpush