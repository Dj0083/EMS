<?php

namespace App\Http\Controllers;

use App\Models\Student;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $studentsData = Student::orderBy('Competitor_no', 'ASC')->paginate(50);
        return view('pages.Competitors.student_list', ['students' => $studentsData]);
    }

    public function fetchAll()
    {
        $students = Student::all();
        $output = '';
        if ($students->isNotEmpty()) {
            $output .= '<table class="table table-striped align-middle">
                <thead>
                  <tr>
                    <th>#</th>
                    <th>Id</th>
                    <th>Name</th>
                    <th>Year</th>
                    <th>School</th>
                    <th>Incharge</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody>';

            foreach ($students as $key => $stu) {
                $output .= '<tr>
                    <td>' . ++$key . '</td>
                    <td>' . $stu->Competitor_no . '</td>
                    <td>' . $stu->Name . '</td>
                    <td>' . $stu->Year . '</td>
                    <td>' . $stu->School . '</td>
                    <td>' . $stu->Incharge . '</td>
                    <td>
                      <a href="#" id="' . $stu->id . '" class="btn btn-primary btn-sm mx-1 editIcon"
                        data-bs-toggle="modal" data-bs-target="#editCompetitorModel"><i class="bx bx-edit"></i></a>
                      <a href="#" id="' . $stu->id . '" class="btn btn-danger btn-sm mx-1 deleteIcon"><i class="bx bxs-trash"></i></a>
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
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    // Make sure this is at the top

    public function store(Request $request)
    {
        $value = $request->com_id;
        $year = $request->com_year;

        // check if Competitor_no already exists in the database
        $existingData = Student::where('Competitor_no', $value)
            ->where('Year', $year)
            ->first();

        if ($existingData) {
            return response()->json([
                'success' => false,
                'message' => $value . ' already exists in the database',
                'title'   => 'Found Duplicate',
                'type'    => 'info'
            ]);
        }

        // Calculate average here
        $average = ($request->com_attempt_1 + $request->com_attempt_2) / 2;

        $data = [
            'Competitor_no'      => $request->com_id,
            'Name'               => $request->com_name,
            'School'             => $request->com_school,
            'Incharge'           => $request->com_incharge,
            'Attempt_1'          => $request->com_attempt_1,
            'Attempt_2'          => $request->com_attempt_2,
            'Average'            => $average, // use backend value
            'Email'              => $request->com_email,
            'Contact'            => $request->com_contact,
            'Year'               => $request->com_year,
            'certificate_status' => $request->certificate_status,
        ];

        Student::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Student added successfully',
            'title'   => 'Saved!',
            'type'    => 'success'
        ]);
    }


    /**
     * Display the specified resource.
     */
    public function show(Student $student)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request)
    {
        $student = Student::findOrFail($request->id);
        return response()->json($student);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        $id = $request->com_id; // hidden field in modal
        $com_id = $request->edit_competitor_id;
        $com_name = $request->edit_competitor_name;
        $com_school = $request->edit_competitor_school;
        $com_incharge = $request->edit_competitor_incharge;

        //Check for duplicates (Competitor_no + Name + School)
        $existingData = Student::where('Competitor_no', $com_id)
            ->where('Name', $com_name)
            ->where('School', $com_school)
            ->where('Incharge', $com_incharge)
            // ->where('id', '!=', $id) // exclude current record
            ->first();

        if ($existingData) {
            return response()->json([
                'success' => false,
                'message' => 'This competitor already exists in the database',
                'title'   => 'Found Duplicate',
                'type'    => 'info'
            ]);
        }

        //Prepare update data
        $update_data = [
            'Competitor_no' => $com_id,
            'Name'          => $com_name,
            'School'        => $com_school,
            'Incharge'      => $com_incharge,
        ];

        Student::find($id)->update($update_data);

        return response()->json([
            'success' => true,
            'message' => 'Competitor updated successfully',
            'title'   => 'Updated!',
            'type'    => 'success'
        ]);
    }



    /**
     * Remove the specified resource from storage.
     */
    public function delete(Request $request)
    {
        Student::destroy($request->id);
        return response()->json(['success' => true]);
    }


    public function getStudent()
    {
        $student = Student::select('*')->get();
        return response()->json($student);
    }
}
