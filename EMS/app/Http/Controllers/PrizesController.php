<?php

namespace App\Http\Controllers;

use App\Models\Prizes;
use Illuminate\Http\Request;

class PrizesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $prizedata = Prizes::orderby('Position', 'ASC')->paginate(50);
        return view('pages.Competitors.Prizes_list', ['prizes' => $prizedata]);
    }

    public function fetchAll()
    {
        $prizes = Prizes::all();

        $output = '';
        if ($prizes->isNotEmpty()) {
            $output .= '
    <table class="table table-striped align-middle">
        <thead>
            <tr>
                <th>#</th>
                <th>Place</th>
                <th>Prize Amount</th>
                <th>Year</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>';

            foreach ($prizes as $key => $prize) {
                $output .= '
            <tr>
                <td>' . ($key + 1) . '</td>
                <td>' . $prize->Position . '</td>
                <td>' . $prize->Prize_amount . '</td>
                <td>' . $prize->Year . '</td>
                <td>
                    <a href="#" id="' . $prize->id . '" class="btn btn-sm btn-primary editIcon">
                        <i class="bx bx-edit"></i>
                    </a>
                    <a href="#" id="' . $prize->id . '" class="btn btn-sm btn-danger deleteIcon">
                        <i class="bx bx-trash"></i>
                    </a>
                </td>
            </tr>';
            }

            $output .= '
        </tbody>
    </table>';
        } else {
            $output = '<h5 class="text-center text-muted">No Prizes Found</h5>';
        }

        return response()->json(['data' => $output]);
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
        $value = $request->prize_id;
        $year = $request->prize_year;
        $existing = Prizes::where('Position', $value)
            ->where('Year', $year)
            ->first();

        if ($existing) {
            return response()->json(['success' => false, 'message' => $value . ' already exists', 'title' => 'Duplicate Found', 'type' => 'info']);
        }

        $data = [
            'Position' => $request->prize_num,
            'Prize_amount' => $request->prize_amount,
            'Year' => $request->prize_year,
        ];

        Prizes::create($data);
        return response()->json(['success' => true, 'message' => 'Prize added successfully', 'title' => 'Saved!', 'type' => 'success']);
    }

    /**
     * Display the specified resource.
     */
    public function show(Prizes $prizes)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request)
    {
        $prize = Prizes::find($request->id);
        if (!$prize) {
            return response()->json(['error' => 'Prize not found'], 404);
        }
        return response()->json($prize);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        $id = $request->pri_id;
        $prize_id = $request->edit_prize_id;
        $prize_amount = $request->edit_prize_amount;

        $existing = Prizes::where('Position', $prize_id)
            ->where('Prize_amount', $prize_amount)
            ->first();

        if ($existing) {
            return response()->json([
                'success' => false,
                'message' => 'This Prize already exists',
                'title' => 'Duplicate Found',
                'type' => 'info'
            ]);
        }

        Prizes::find($id)->update([
            'Position' => $prize_id,
            'Prize_amount' => $prize_amount,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Prize updated successfully',
            'title' => 'Updated!',
            'type' => 'success'
        ]);
    }


    /**
     * Remove the specified resource from storage.
     */
    public function delete(Request $request)
    {
        Prizes::destroy($request->id);
        return response()->json(['success' => true]);
    }


    public function getStudent()
    {
        $prize = Prizes::select('*')->get();
        return response()->json($prize);
    }
}
