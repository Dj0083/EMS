<?php

namespace App\Http\Controllers;

use App\Models\ArtCompetitors;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class ArtCompetitorsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $studentsData = ArtCompetitors::orderBy('Competitor_no', 'ASC')->paginate(50);
        return view('pages.Competitors.ArtCompetitors', ['artcompetitors' => $studentsData]);
    }

    public function fetchAll()
    {
        $competitors = ArtCompetitors::all();
        $output = '';

        if ($competitors->isNotEmpty()) {
            $output .= '<table class="table table-striped align-middle">
                <thead>
                  <tr>
                    <th>#</th>
                    <th>Id</th>
                    <th>Name</th>
                    <th>Year</th>
                    <th>School</th>
                    <th>Incharge</th>
                    <th>Art</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody>';

            foreach ($competitors as $key => $com) {
                $output .= '<tr>
                    <td>' . ++$key . '</td>
                    <td>' . $com->Competitor_no . '</td>
                    <td>' . $com->Name . '</td>
                    <td>' . $com->Year . '</td>
                    <td>' . $com->School . '</td>
                    <td>' . $com->Incharge . '</td>
                    <td>
                        <i class="bx bx-image view-img-icon" data-id="' . $com->id . '" title="View Image"></i>
                    </td>
                    <td>
                      <a href="#" id="' . $com->id . '" class="btn btn-primary btn-sm mx-1 editIcon"
                        data-bs-toggle="modal" data-bs-target="#editCompetitorModel"><i class="bx bx-edit"></i></a>
                      <a href="#" id="' . $com->id . '" class="btn btn-danger btn-sm mx-1 deleteIcon"><i class="bx bxs-trash"></i></a>
                    </td>
                  </tr>';
            }

            $output .= '</tbody></table>';
            echo $output;
        } else {
            echo '<h4 class="text-center text-secondary my-5">No records in the database!</h4>';
        }
    }

    /**
     * Store a newly created resource.
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'arcom_id'     => 'required',
                'arcom_name'   => 'required|string|max:255',
                'arcom_school' => 'nullable|string|max:255',
                'arcom_incharge' => 'nullable|string|max:255',
                'arcom_place'  => 'nullable|string|max:255',
                'arcom_prize'  => 'nullable|string|max:255',
                'arcom_email'  => 'nullable|email',
                'arcom_contact' => 'nullable|string|max:20',
                'arcom_year'   => 'required|numeric|min:2020',
                'certificate_status' => 'nullable|string',
                'art_file'     => 'required|mimes:pdf,doc,docx,jpg,jpeg,png|max:5120',
            ]);

            if (!$request->hasFile('art_file')) {
                return response()->json(['success' => false, 'message' => 'No file uploaded']);
            }

            $file = $request->file('art_file');

            if (!$file->isValid()) {
                return response()->json(['success' => false, 'message' => 'Invalid file']);
            }

            $existing = ArtCompetitors::where('Competitor_no', $request->arcom_id)
                ->where('Year', $request->arcom_year)
                ->first();

            if ($existing) {
                return response()->json([
                    'success' => false,
                    'message' => "Competitor no {$request->arcom_id} already exists for year {$request->arcom_year}"
                ]);
            }

            // ✅ SAVE TO public/competitor_files
            $destination = public_path('competitor_files');

            if (!File::exists($destination)) {
                File::makeDirectory($destination, 0755, true);
            }

            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move($destination, $filename);

            $competitor = new ArtCompetitors();
            $competitor->Competitor_no      = $request->arcom_id;
            $competitor->Name               = $request->arcom_name;
            $competitor->School             = $request->arcom_school;
            $competitor->Incharge           = $request->arcom_incharge;
            $competitor->Place              = $request->arcom_place;
            $competitor->Prize              = $request->arcom_prize;
            $competitor->Email              = $request->arcom_email;
            $competitor->Contact            = $request->arcom_contact;
            $competitor->Year               = $request->arcom_year;
            $competitor->certificate_status = $request->certificate_status;
            $competitor->Path               = $filename; // ONLY filename
            $competitor->save();

            return response()->json([
                'success' => true,
                'message' => 'Competitor added successfully!',
                'data'    => $competitor
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function getImage(Request $request)
    {
        $competitor = ArtCompetitors::find($request->id);

        if (!$competitor || !$competitor->Path) {
            return response()->json(['success' => false]);
        }

        // ✅ PUBLIC PATH
        $imageUrl = asset('competitor_files/' . $competitor->Path);

        return response()->json([
            'success' => true,
            'image_url' => $imageUrl
        ]);
    }

    public function edit(Request $request)
    {
        $competitors = ArtCompetitors::findOrFail($request->id);
        return response()->json($competitors);
    }

    public function update(Request $request)
    {
        $id = $request->com_id;

        $existingData = ArtCompetitors::where('Competitor_no', $request->edit_competitor_id)
            ->where('Name', $request->edit_competitor_name)
            ->where('School', $request->edit_competitor_school)
            ->where('Incharge', $request->edit_competitor_incharge)
            ->first();

        if ($existingData) {
            return response()->json([
                'success' => false,
                'message' => 'This competitor already exists in the database',
                'title' => 'Found Duplicate',
                'type' => 'info'
            ]);
        }

        ArtCompetitors::find($id)->update([
            'Competitor_no' => $request->edit_competitor_id,
            'Name' => $request->edit_competitor_name,
            'School' => $request->edit_competitor_school,
            'Incharge' => $request->edit_competitor_incharge,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Competitor updated successfully',
            'title' => 'Updated!',
            'type' => 'success'
        ]);
    }

    public function delete(Request $request)
    {
        $competitor = ArtCompetitors::find($request->id);

        if ($competitor && $competitor->Path) {
            $filePath = public_path('competitor_files/' . $competitor->Path);
            if (File::exists($filePath)) {
                File::delete($filePath);
            }
        }

        ArtCompetitors::destroy($request->id);
        return response()->json(['success' => true]);
    }

    public function getStudent()
    {
        return response()->json(ArtCompetitors::all());
    }
}
