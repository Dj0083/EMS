<?php

namespace App\Http\Controllers;

use App\Models\ScoreCard2;
use Illuminate\Http\Request;

class ScoreCard2Controller extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $defaultYear = date('Y');

        $participants = ScoreCard2::where('Year', $defaultYear)
            ->orderBy('Place', 'ASC')
            ->get();

        $firstPlace = $participants->where('Place', 1)->first();
        $secondPlace = $participants->where('Place', 2)->first();
        $thirdPlace = $participants->where('Place', 3)->first();

        return view('pages.Competitors.ScoreCard2_list', compact(
            'firstPlace',
            'secondPlace',
            'thirdPlace',
            'participants',
            'defaultYear'
        ));
    }

    /**
     * Filter participants by year (AJAX)
     */
    public function filterByYear(Request $request)
    {
        $year = $request->year ?? date('Y');

        $participants = ScoreCard2::where('Year', $year)
            ->orderBy('Place', 'ASC')
            ->get();

        $firstPlace = $participants->where('Place', 1)->first();
        $secondPlace = $participants->where('Place', 2)->first();
        $thirdPlace = $participants->where('Place', 3)->first();

        return response()->json([
            'participants' => $participants,
            'firstPlace' => $firstPlace,
            'secondPlace' => $secondPlace,
            'thirdPlace' => $thirdPlace,
        ]);
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(ScoreCard2 $scoreCard2)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ScoreCard2 $scoreCard2)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ScoreCard2 $scoreCard2)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ScoreCard2 $scoreCard2)
    {
        //
    }
}
