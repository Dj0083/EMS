<?php

namespace App\Http\Controllers;

use App\Models\Prizes;
use App\Models\ScoreCard1;
use Illuminate\Http\Request;

class ScoreCard1Controller extends Controller
{
    /**
     * Display a listing of the resource.
     */
    // at top of file: use Illuminate\Http\Request; (you already have this)

    public function index(Request $request)
    {
        $defaultYear = $request->input('year', date('Y'));

        // Fetch participants for the year
        $participants = ScoreCard1::where('Year', $defaultYear)
            ->get()
            ->map(function ($p) {
                $p->average = ((float)$p->Attempt_1 + (float)$p->Attempt_2) / 2;
                return $p;
            });

        // Fetch prizes from Prizes table for the year
        $prizes = Prizes::where('Year', $defaultYear)->get()->keyBy('Position');

        // ✅ Sort participants by average ascending (lowest avg = best)
        $sorted = $participants->sortBy('average')->values();

        // Assign place and prize
        foreach ($sorted as $index => $p) {
            $place = $index + 1;
            $p->place = $place;
            $p->prize = $prizes[$place]->Prize_amount ?? '---';
        }

        // Top 3
        $firstPlace  = $sorted[0] ?? null;
        $secondPlace = $sorted[1] ?? null;
        $thirdPlace  = $sorted[2] ?? null;

        // Best Shooter: participant with min distance from either attempt
        $bestShooter = $participants->sortBy(fn($p) => min((float)$p->Attempt_1, (float)$p->Attempt_2))->first();

        return view('pages.Competitors.ScoreCard1_list', [
            'participants' => $sorted,
            'firstPlace'   => $firstPlace,
            'secondPlace'  => $secondPlace,
            'thirdPlace'   => $thirdPlace,
            'bestShooter'  => $bestShooter,
            'defaultYear'  => $defaultYear,
        ]);
    }


    public function filterByYear(Request $request)
    {
        $year = $request->year ?? date('Y');

        // Fetch participants
        $participants = ScoreCard1::where('Year', $year)
            ->get()
            ->map(function ($p) {
                $p->average = ((float)$p->Attempt_1 + (float)$p->Attempt_2) / 2; // average for place
                return $p;
            });

        // Fetch prizes
        $prizes = Prizes::where('Year', $year)->get()->keyBy('Position');

        // Sort by average ascending (lowest avg = 1st place)
        $sorted = $participants->sortBy('average')->values();

        // Assign place and prize for all participants
        foreach ($sorted as $index => $p) {
            $place = $index + 1;
            $p->place = $place;
            $p->prize = $prizes[$place]->Prize_amount ?? '---';
        }

        // Top 3 for cards
        $firstPlace  = $sorted[0] ?? null;
        $secondPlace = $sorted[1] ?? null;
        $thirdPlace  = $sorted[2] ?? null;

        // Best Shooter: participant with minimum distance from either attempt
        $bestShooter = $participants->sortBy(fn($p) => min((float)$p->Attempt_1, (float)$p->Attempt_2))->first();

        return response()->json([
            'participants' => $sorted,    // all participants
            'firstPlace'   => $firstPlace,
            'secondPlace'  => $secondPlace,
            'thirdPlace'   => $thirdPlace,
            'bestShooter'  => $bestShooter,
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
    public function show(ScoreCard1 $scoreCard1)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ScoreCard1 $scoreCard1)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ScoreCard1 $scoreCard1)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ScoreCard1 $scoreCard1)
    {
        //
    }
}
