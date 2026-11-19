@extends('layouts.app')

@section('title')
Criteria List
@endsection

@push('css')
<style>
    .container {
        overflow-y: auto;
    }
</style>
@endpush

@section('content')
<x-title> Competition Criteria's</x-title>

<div class="container-fluid p-3 mb-3" style="min-height: 80vh;">
    <div class="row">


        {{-- table --}}
        <div class="col-12 col-md-7 order-last order-md-first ">
            <div style="height: 100vh;">
                <div class="card border-0 shadow-lg" style="max-height: 100vh; overflow-y: auto;">
                    <div class="card-body" id="criteria">
                        {{-- Criteria table goes here --}}
                    </div>
                </div>
            </div>
        </div>

        {{-- form --}}
        <div class="col-12 col-md-5 mt-4 order-first order-md-last mb-4 mb-md-0">
            <div>
                <div class="card border-0">
                    <form id="new_competitor_adding" action="{{ route('artcomcriteria.store') }}" method="post" enctype="multipart/form-data">
                        @csrf
                        <div class="card border-0 shadow-lg p-3">
                            <div class="card-header">
                                <h5 class="card-title">
                                    <i class='bx bx-add-to-queue'></i>
                                    Add Criteria.
                                </h5>
                            </div>
                            <div class="card-body">

                                <div class="mb-3">
                                    <label for="competition" class="form-label">Select Competition</label>
                                    <select name="competition" id="competition" class="form-select">
                                        <option value="">-- Select Competition --</option>
                                        <option value="art" {{ old('competition') == 'Art_Competiton' ? 'selected' : '' }}>
                                            Art Competition
                                        </option>
                                        <option value="water_rocket" {{ old('competition') == 'Water_Rocket' ? 'selected' : '' }}>
                                            Water Rocket Competition
                                        </option>
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label for="cri_file" class="form-label">Event Criteria</label>
                                    <input type="file" min="0" name="cri_file" id="cri_file" placeholder="Enter Competitor ID"
                                        class="form-control" required>
                                </div>
                                <div class="mb-3">
                                    <label for="cri_year" class="form-label">Year</label>
                                    <input type="number" min="2025" name="cri_year" id="cri_year"
                                        placeholder="Enter year" class="form-control" required>
                                </div>
                                <x-submit-btn class="mt-3 float-end" id="add">Add</x-submit-btn>
                                <x-reset-btn class="mt-3 mx-2 float-end" />
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection

@push('js')
@include("jQuery.ArtComCriteria_jQ")
@endpush