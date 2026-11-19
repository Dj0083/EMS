@extends('layouts.app')

@section('title')
Prizes List
@endsection

@push('css')
<style>
    .container {
        overflow-y: auto;
    }
</style>
@endpush

@section('content')
<x-title>Prizes List</x-title>

{{-- Top Left Buttons --}}
<div class="mb-3 d-flex justify-content-start gap-2 px-3">
    <a href="{{ route('student.index') }}" class="btn btn-secondary">
        <i class='bx bx-arrow-back'></i> Back
    </a>
</div>

<div class="container p-3 mb-3" style="min-height: 80vh;">
    <div class="row">

        {{-- table --}}
        <div class="col-12 col-md-7 order-last order-md-first ">
            <div style="height: 50vh;">
                <div class="card border-0 shadow-lg" style="max-height: 80vh; overflow-y: auto;">
                    <div class="card-body" id="prizes">
                        {{-- Prize table goes here --}}
                    </div>
                </div>
            </div>
        </div>

        {{-- form --}}
        <div class="col-12 col-md-5 mt-4 order-first order-md-last mb-4 mb-md-0">
            <div>
                <div class="card border-0">
                    <form id="new_prize_adding" action="#" method="post" enctype="multipart/form-data">
                        @csrf
                        <div class="card border-0 shadow-lg p-3">
                            <div class="card-header">
                                <h5 class="card-title">
                                    <i class='bx bx-add-to-queue'></i>
                                    Add Prizes.
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label for="prize_num" class="form-label">Place</label>
                                    <input type="number" min="1" name="prize_num" id="prize_num"
                                        placeholder="Enter Place Ex: 1" class="form-control" required>
                                </div>
                                <div class="mb-3">
                                    <label for="prize_amount" class="form-label">Prize Amount</label>
                                    <input type="text" name="prize_amount" id="prize_amount"
                                        placeholder="Enter Prize Amount" class="form-control" required>
                                </div>
                                <div class="mb-3">
                                    <label for="prize_year">Year</label>
                                    <input type="number" min="2025" name="prize_year" id="prize_year"
                                        class="form-control" placeholder="Enter Year Ex: 2025" required>
                                </div>

                                <x-submit-btn class="mt-3 float-end" id="add">Add</x-submit-btn>
                                <x-reset-btn class="mt-3 mx-2 float-end" />
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Edit Student modal --}}
        <div class="modal fade" id="editPrizeModel" tabindex="-1" aria-labelledby="exampleModalLabel"
            data-bs-backdrop="static" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Prizes</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form action="#" method="post" id="edit_prize_form" enctype="multipart/form-data">
                        @csrf
                        <div class="modal-body p-4 bg-light">
                            <div class="col-lg">
                                <label for="edit_prize_id">Prize No</label>
                                <input type="text" name="pri_id" id="prize_id" hidden="true">
                                <input type="text" name="edit_prize_id" id="edit_prize_id" class="form-control"
                                    placeholder="Enter Prize No" required>
                            </div>
                            <div class="col-lg">
                                <label for="edit_prize_amount">Prize Amount</label>
                                <input type="text" name="edit_prize_amount" id="edit_prize_amount"
                                    class="form-control" placeholder="Enter Prize amount" required>
                            </div>

                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" id="edit_prize_btn" class="btn btn-success">Update</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection

@push('js')
@include("jQuery.Prizes_list_jQ")
@endpush