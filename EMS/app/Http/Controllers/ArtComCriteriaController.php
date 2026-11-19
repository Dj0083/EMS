<?php

namespace App\Http\Controllers;

use App\Models\ArtComCriteria;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class ArtComCriteriaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $criteria = ArtComCriteria::orderBy('Year', 'desc')->get()->map(function ($cri) {
                $cri->FileUrl = asset('competitor_files/' . $cri->Path);
                return $cri;
            });
            return response()->json($criteria);
        }

        $criteria = ArtComCriteria::orderBy('Year', 'desc')->get();
        return view('pages.Competitors.ArtComCriteria', compact('criteria'));
    }

    /**
     * Fetch all criteria (returns HTML table).
     */
    public function fetchAll()
    {
        $criteria = ArtComCriteria::orderBy('Year', 'desc')->get();

        if ($criteria->isEmpty()) {
            return '<h4 class="text-center text-secondary my-5">No records in the database!</h4>';
        }

        $output = '
        <table class="table table-striped align-middle">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Competition</th>
                    <th>View</th>
                    <th>Year</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>';

        foreach ($criteria as $key => $cri) {
            $fileUrl = asset('competitor_files/' . $cri->Path);

            $output .= '<tr>
                <td>' . ++$key . '</td>
                <td>' . e($cri->Com_Type) . '</td>
                <td><a href="' . $fileUrl . '" target="_blank">View File</a></td>
                <td>' . e($cri->Year) . '</td>
                <td>
                    <button class="btn btn-danger btn-sm delete-btn" data-id="' . $cri->id . '">
                        <i class="bx bxs-trash"></i>
                    </button>
                </td>
            </tr>';
        }

        $output .= '</tbody></table>';
        return $output;
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'cri_file' => 'required|mimes:pdf,doc,docx,jpg,png,jpeg|max:5120',
                'cri_year' => 'required|numeric|min:2025',
            ]);

            $file = $request->file('cri_file');

            // Destination: public/competitor_files
            $destinationPath = public_path('competitor_files');

            if (!File::exists($destinationPath)) {
                File::makeDirectory($destinationPath, 0755, true);
            }

            $fileName = time() . '_' . $file->getClientOriginalName();
            $file->move($destinationPath, $fileName);

            $criteria = new ArtComCriteria();
            $criteria->Com_Type = $request->competition;
            $criteria->Path = $fileName; // only filename stored
            $criteria->Year = $request->cri_year;
            $criteria->save();

            return response()->json([
                'success' => true,
                'message' => 'Criteria added successfully!',
                'data' => [
                    'id' => $criteria->id,
                    'FileUrl' => asset('competitor_files/' . $criteria->Path),
                    'Year' => $criteria->Year,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Delete a criteria record.
     */
    public function delete(Request $request)
    {
        $criteria = ArtComCriteria::find($request->id);

        if (!$criteria) {
            return response()->json(['success' => false, 'message' => 'Record not found']);
        }

        $filePath = public_path('competitor_files/' . $criteria->Path);

        if (File::exists($filePath)) {
            File::delete($filePath);
        }

        $criteria->delete();

        return response()->json(['success' => true]);
    }

    /**
     * Get criteria (JSON)
     */
    public function getCriteria()
    {
        $competitor = ArtComCriteria::all()->map(function ($cri) {
            $cri->FileUrl = asset('competitor_files/' . $cri->Path);
            return $cri;
        });

        return response()->json($competitor);
    }
}
