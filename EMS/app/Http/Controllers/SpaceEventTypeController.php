<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\EventType;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class SpaceEventTypeController extends Controller
{
    public function index()
    {
        return view('pages.space_event_type.space_event_type_list');
    }

    public function showTasks($event)
    {
        $tasks = Task::where('event_type_id', $event)->get();
        $event = EventType::where('id', $event)->first();

        return view(
            'pages.task.tasks_per_event',
            [
                'tasks' => $tasks,
                'event' => $event
            ]
        );
    }

    public function fetchAll()
    {
        $eventTypes = EventType::all();
        $output = '';
        if ($eventTypes->count() > 0) {
            $output .= '<table class="table table-striped align-middle">
            <thead>
              <tr>
                <th>ID</th>
                <th>Event Type</th>
                <th>Image</th>
                <th>Action</th>
                <th>Tasks</th>
              </tr>
            </thead>
            <tbody>';
            foreach ($eventTypes as $key => $eventType) {
                $img_path = $eventType->img_path;
                $imgPath = asset('uploads/' . $img_path); // updated the path
                $output .= '<tr>
                <td>' . ++$key . '</td>
                <td>' . $eventType->event_type . '</td>
                <td>
                    <a href="#" id="' . $eventType->id . '" class="btn btn-success btn-sm mx-1 imageIcon" title="show image" onclick-="showImage(\'' . $eventType->id . '\')"><i class="bx bx-image" style="font-size: 22px"></i></a>
                </td>
                <td>
                  <a href="#" 
                        class="btn btn-primary btn-sm mx-1 editIcon" 
                        data-id="' . $eventType->id . '" 
                        data-bs-toggle="modal" 
                        data-bs-target="#editEventTypeModel" 
                        title="edit">
                            <i class="bx bx-edit" style="font-size: 22px"></i>
                        </a>
                    <a href="#" id="' . $eventType->id . '" class="btn btn-danger btn-sm mx-1 deleteIcon" title="delete"><i class="bx bxs-trash" style="font-size: 22px"></i></a>
                </td>
                <td>
                    <a href="' . route('event_type.tasks', $eventType->id) . '" class="btn btn-success rounded">
                         View Tasks
                    </a>

                    <input type="hidden" name="imgPath[]" id="imgPath_' . $eventType->id . '" value="' . $imgPath . '">
                </td>
              </tr>';
            }
            $output .= '</tbody></table>';
            echo $output;
        } else {
            echo '<h4 class="text-center text-secondary my-5">No record in the database!</h4>';
        }
    }

    public function store(Request $request)
    {
        $value = $request->event_type;

        $existInData = EventType::where('event_type', $value)->first();
        if ($existInData) {
            return response()->json([
                'success' => false,
                'message' => $value . ' already exists in the database',
                'title' => 'Found Duplicate',
                'type' => 'info'
            ]);
        }

        //Default image path
        $img_path = 'uploads/default.jpg';

        if ($request->hasFile('image')) {
            $validator = Validator::make($request->all(), [
                'image' => 'mimes:jpg,jpeg,png,webp,gif,svg|max:2048' // validation based on extention format
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => $validator->errors()->first(),
                    'title' => 'Unauthorized file Type',
                    'type' => 'error'
                ]);
            }


            $file = $request->file('image');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads'), $fileName);
            $img_path = 'uploads/' . $fileName;
        }

        $newRecord = EventType::create([
            'event_type' => $value,
            'img_path'   => $img_path
        ]);

        return response()->json([
            'success'     => true,
            'message'     => 'Event type added successfully',
            'title'       => 'Saved!',
            'type'        => 'success',
            'new_record'  => $newRecord->id
        ]);
    }

    public function showImage(Request $request)
    {
        $eventType = EventType::findOrFail($request->id);

        //Accessing the public path to retriew and display images(working).
        $imageUrl = asset($eventType->img_path);

        return response()->json([
            'image_path' => $imageUrl
        ]);
    }

    public function update(Request $request) // update image 
    {
        $event_type = EventType::find($request->event_id);

        $original_value = $request->original_event_type;
        $value = $request->edit_event_type;
        $existInData = EventType::where('event_type', $value)->first();

        if ($original_value !== $value) {
            if ($existInData) {
                return response()->json([
                    'success' => false,
                    'message' => $value . ' already exists in the database',
                    'title' => 'Found Duplicate',
                    'type' => 'info'
                ]);
            }
        }

        if ($request->hasFile('image')) {
            $file_path = $event_type->img_path;

            if ($file_path != 'uploads/default.jpg') {
                if (file_exists(public_path($file_path))) {
                    unlink(public_path($file_path));
                }
                Log::info('Image Deleted');
                //image deleting
            }

            $validator = Validator::make($request->all(), [
                'image' => 'mimes:jpg,jpeg,png,webp,gif,svg'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => $validator->errors()->first(),
                    'title' => 'Unauthorized file Type',
                    'type' => 'error'
                ]);
            }

            $file = $request->file('image');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads'), $fileName);
            $img_path = 'uploads/' . $fileName;

            $update_data = [
                'event_type' => $request->edit_event_type,
                'img_path' => $img_path,
                'updated_at' => Carbon::now()
            ];
        } else {
            $update_data = [
                'event_type' => $request->edit_event_type,
                'updated_at' => Carbon::now()
            ];
        }

        $event_type->update($update_data);

        return response()->json([
            'success' => true,
            'title' => 'Updated!',
            'message' => 'Event type updated successfully.',
            'type' => 'success'
        ]);
    }

    /* public function updateEventType(Request $request)
    {
        $request->validate([
            'event_id'      => 'required|exists:event_types,id',
            'event_type'    => 'required|string|max:255',
            'image'         => 'nullable|image|mimes:jpg,jpeg,png,webp,gif,svg|max:2048',
        ]);

        $eventType = EventType::findOrFail($request->event_id);

        // check for duplicates (only if event_type changed)
        if ($eventType->event_type !== $request->event_type) {
            $exists = EventType::where('event_type', $request->event_type)->first();
            if ($exists) {
                return response()->json([
                    'success' => false,
                    'title'   => 'Duplicate Found',
                    'message' => $request->event_type . ' already exists in the database',
                    'type'    => 'info',
                ]);
            }
        }

        $updateData = [
            'event_type' => $request->event_type,
            'updated_at' => Carbon::now(),
        ];

        // Handle new image upload
        if ($request->hasFile('image')) {
            // delete old image (if not default)
            if ($eventType->img_path && $eventType->img_path !== 'uploads/default.jpg') {
                if (file_exists(public_path($eventType->img_path))) {
                    unlink(public_path($eventType->img_path));
                }
            }

            $file = $request->file('image');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads'), $fileName);
            $updateData['img_path'] = 'uploads/' . $fileName;
        }

        $eventType->update($updateData);

        return response()->json([
            'success' => true,
            'title'   => 'Updated!',
            'message' => 'Event type updated successfully.',
            'type'    => 'success'
        ]);
    }*/


    public function delete(Request $request)
    {
        $id = $request->id;

        $taskCount = Task::where('event_type_id', $id)->count();
        if ($taskCount > 0) {
            return response()->json(['success' => false, 'title' => 'Error', 'message' => 'This Event type has related tasks. Delete the tasks first before deleting the event type.', 'type' => 'error']);
        }

        $event = EventType::find($id);
        $img_path = $event->img_path;
        if ($img_path != 'uploads/default.jpg') {
            if (file_exists(public_path($img_path))) {
                unlink(public_path($img_path));
            }
            Log::info('Image Deleted');
        }

        EventType::destroy($id);
        return response()->json(['success' => true]);
    }

    public function edit($id)
    {
        $eventType = EventType::findOrFail($id);

        return response()->json([
            'id' => $eventType->id,
            'event_type' => $eventType->event_type,
            'img_path' => $eventType->img_path,
        ]);
    }
}
