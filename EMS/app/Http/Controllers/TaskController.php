<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Task;
use App\Models\EventType;
use App\Models\Attachment;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class TaskController extends Controller
{
    public function index()
    {
        $event_types = EventType::all();
        return view('pages.task.task', compact('event_types'));
    }

    public function task_form(Request $request)
    {
        $atc = new attachment();
        $list = new Task();

        $file_name = time() . '.' . request()->image->getClientOriginalExtension();
        request()->image->move(public_path('images'), $file_name);

        $list->task_name = $request->task_name;
        // $atc->task_no = $request->task_no;
        $atc->image = $file_name;
        $list->save();
        return response()->json(['success' => true]);
    }

    /**
     * store tasks or both tasks and attachments if a task has attachments.
     *
     * @param Request $request The incoming request containing task details and attachments.
     * @return \Illuminate\Http\JsonResponse The response containing success status, message, title, and type.
     * @author Hansana Sandeepa <hansana.s.somarathna@gmail.com>
     */
    public function store(Request $request)
    {
        $task_name = $request->task_name;
        $eventId = $request->input('event_id');
        $attachments = $request->file('attachments');

        //check if the value already exists in the database
        $existingData = Task::where('task_name', $task_name)
            ->where('event_type_id', $eventId)
            ->exists();

        if ($existingData) {
            return response()->json([
                'success' => false,
                'message' => $task_name . ' already exists in the database',
                'title' => 'Found Duplicate',
                'type' => 'error'
            ]);
        }

        //validate request
        $fileValidator = Validator::make(
            $request->all(),
            [
                'task_name' => 'required|string|max:255',
                'attachments.*' => 'mimes:pdf,docx,doc'
            ]
        );

        if ($fileValidator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $fileValidator->errors()->first(),
                'title' => 'Validation Error',
                'type' => 'error'
            ]);
        }


        //Data store process
        $taskData = [
            'task_name' => $task_name,
            'event_type_id' => $eventId,
        ];
        $newTask = Task::create($taskData);
        $newTaskId = $newTask->id;
        Log::info("task created");

        //atachment store process
        $this->processAttachments($attachments, $newTaskId);

        return response()->json([
            'success' => true,
            'message' => 'Task Created successfully',
            'title' => 'Saved!',
            'type' => 'success'
        ]);
    }

    /**
     * Fetches tasks related to a specific event type.
     *
     * This function retrieves tasks from the database based on the provided event type ID.
     * It generates an HTML table to display the tasks, their attachments, and relevant actions.
     *
     * @param mixed $id The ID of the event type for which tasks need to be fetched.
     * @return void
     * @author Hansana Sandeepa <hansana.s.somarathna@gmail.com>
     */
    public function fetchTasks($id)
    {
        $tasks = Task::where('event_type_id', $id)->get();

        $output = '';
        if ($tasks->count() > 0) {
            $output .= '<table class="table align-middle text-center task-table">
            <colgroup>
                <col style="width: 8%">
                <col style="width: 44%">
                <col style="width: 16%">
                <col style="width: 16%">
                <col style="width: 16%">
            </colgroup>
            <thead>
            <tr class="border-dark ">
                <th class="align-middle" >#</th>
                <th class="align-middle" >Tasks</th>
                <th class="align-middle" >Manage Tasks</th>
                <th class="align-middle" >Attachments</th>
                <th class="align-middle" >Manage Attachments</th>
            </tr>
            </thead>
            <tbody>';
            foreach ($tasks as $key => $task) {
                $attachments = Attachment::where('task_id', $task->id)->get();

                if ($attachments->count() == 0) {
                    $output .= '
                    <tr class="border-dark">
                        <td>' . ++$key . '</td>
                        <td>' . $task->task_name . '</td>
                        <td>
                            <button id="' . $task->id . '" class="btn btn-primary btn-sm mx-1 my-1 editTask" data-bs-toggle="modal" title="edit tasks"><i class="bx bx-edit" style="font-size: 20px"></i></button>
                        </td>
                        <td>
                            <button id="' . $task->id . '" class="btn btn-success btn-sm mx-1 my-1 addAttach" title="add attachment"><i class="bx bxs-file-plus" style="font-size: 20px"></i></button>
                        </td>
                        <td>
                            <button id="' . $task->id . '" class="btn btn-danger btn-sm mx-1 my-1 delTask" title="delete task"><i class="bx bxs-trash" style="font-size: 20px"></i></button>
                        </td>
                    </tr>';
                } else {
                    $attchCount = $attachments->count();    //number of total attchments in a task
                    $output .= '
                    <tr class="border-dark">
                        <td rowspan="' . ($attchCount + 1) . '">' . ++$key . '</td>
                        <td rowspan="' . ($attchCount + 1) . '">' . $task->task_name . '</td>
                        <td rowspan="' . ($attchCount + 1) . '" class="align-top">
                            <button id="' . $task->id . '" class="btn btn-primary btn-sm mx-1 my-1 editTask" data-bs-toggle="modal" title="edit tasks"><i class="bx bx-edit" style="font-size: 20px"></i></button>
                        </td>
                        <td>
                            <button id="' . $task->id . '" class="btn btn-success btn-sm mx-1 my-1 addAttach" title="add attachment"><i class="bx bxs-file-plus" style="font-size: 20px"></i></button>
                        </td>
                        <td>
                            <button id="' . $task->id . '" class="btn btn-danger btn-sm mx-1 my-1 delTask" title="delete task"><i class="bx bxs-trash" style="font-size: 20px"></i></button>
                        </td>
                    </tr>';

                    foreach ($attachments as $attachKey => $attachment) {
                        $viewUrl = route('task.viewAttachment', $attachment->id);

                        //counting the modulus of the attachments
                        $keyModulus = $attachKey % 2;
                        $backgroundColor = ($keyModulus == 0) ? "background-color:rgba(117, 194, 255, 0.20)" : "background-color:rgba(194, 194, 194, 0.20)";

                        $border_bottom =  "border-secondary";
                        if ($attachKey == ($attchCount - 1)) {
                            $border_bottom = "border-dark";
                        }
                        $output .= '
                    <tr class=" ' . $border_bottom . ' ">
                        <td style="' . $backgroundColor . '">' . $attachment->attachment . '</td>
                        <td style="' . $backgroundColor . '">
                           <a href="' . $viewUrl . '" target="_blank"
                                class="btn btn-info btn-sm mx-1">
                                <i class="bx bx-show"></i> View File
                            </a>
                            <button type="button"
                                data-id="' . $attachment->id . '"
                                class="btn btn-danger btn-sm mx-1 delAttach">
                                <i class="bx bxs-trash"></i>
                            </button>

                        </td>
                        
                    </tr>';
                    }
                }
            }

            $output .= '</tbody></table>';
            echo $output;
        } else {
            echo '<h4 class="text-center text-secondary my-5">No record in the database!</h4>';
        }
    }

    /**
     * Downloads an attachment based on the provided request.
     *
     * This function retrieves the attachment details from the database using the provided request ID.
     * It then checks if the file exists in the specified storage location.
     * If the file exists, it returns a JSON response containing the success status, file URL, and file name.
     * If the file does not exist, it logs an alert and returns a JSON response with an error message and HTTP status code 404.
     *
     * @param Request $request The incoming request containing the attachment ID.
     * @return \Illuminate\Http\JsonResponse The JSON response indicating the success or failure of the download operation.
     *
     * @author Hansana Sandeepa <hansana.s.somarathna@gmail.com>
     */
    /* public function downloadAttachment(Request $request)
    {
        Log::info("Attachment id is: " . $request->id);

        // find attachment
        $attachment = Attachment::find($request->id);
        if (!$attachment) {
            Log::info("Attachment not found");
            //abort(404, 'Attachment not found');
        }

        $filePath = storage_path('app/public/' . $attachment->path);
        $fileName = $attachment->attachment;

        // check if the file exists
        if (file_exists($filePath)) {
            Log::info("File exists at: " . $filePath);

            // force download
            return response()->download($filePath, $fileName);
        } else {
            abort(404, 'File not found');
        }
    }*/

    public function deleteAttachment(Request $request)
    {
        $attachment = Attachment::findOrFail($request->id);

        $filePath = $attachment->path;
        Log::info('Deleting attachment: ' . $filePath);

        $absolutePath = public_path($filePath);
        if (file_exists($absolutePath)) {
            unlink($absolutePath);
        }
        // Always delete DB record
        $attachment->delete();

        return response()->json([
            'success' => true,
            'message' => 'Attachment deleted successfully.',
            'title' => 'Delete Successful',
            'type' => 'success'
        ], 200);
    }


    /**
     * Retrieves and returns task details for editing.
     *
     * This function retrieves a task record from the database based on the provided ID.
     * It then returns the task details in JSON format for display in an edit modal.
     *
     * @param Request $request The incoming request containing the task ID.
     * @return \Illuminate\Http\JsonResponse The JSON response containing the task details.
     *
     * @author Hansana Sandeepa <hansana.s.somarathna@gmail.com>
     */
    public function edit(Request $request)
    {
        $id = $request->id;
        $task = Task::find($id);
        return response()->json($task);
    }


    /**
     * Updates a task record in the database.
     *
     * This function checks if a task with the same name and event type already exists in the database.
     * If not, it updates the task record with the provided data.
     *
     * @param Request $request The incoming request containing the task details to be updated.
     * @return \Illuminate\Http\JsonResponse The JSON response indicating the success or failure of the update operation.
     *
     * @author Hansana Sandeepa <hansana.s.somarathna@gmail.com>
     */
    public function update(Request $request)
    {
        //check whether the task already exists
        $existingData = Task::where('task_name', $request->taskName)
            ->where('event_type_id', $request->event_type_id)
            ->exists();

        if ($existingData) {
            return response()->json([
                'success' => false,
                'message' => 'Task with the same name already exists in the database',
                'title' => 'Found Duplicate',
                'type' => 'error'
            ]);
        }
        //update database
        Task::find($request->task_id)->update([
            'task_name' => $request->taskName,
            'event_type_id' => $request->event_type_id,
            'updated_at' => Carbon::now()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Task updated successfully',
            'title' => 'Update Successful',
            'type' => 'success'
        ]);
    }

    /**
     * Adds attachments related to a task.
     *
     * This function validates the uploaded attachments, processes the file names and uploads the files,
     * and creates corresponding records in the database.
     *
     * @param Request $request The incoming request containing the uploaded attachments and task ID.
     * @return \Illuminate\Http\JsonResponse The JSON response indicating the success or failure of the operation.
     *
     * @author Hansana Sandeepa <hansana.s.somarathna@gmail.com>
     */
    public function addAttachment(Request $request)
    {
        //validate file type
        if ($request->hasFile('attachments')) {
            $uploadedFile = $request->file('attachments');
            $fileValidator = Validator::make(
                $request->all(),
                [
                    'attachments.*' => 'mimes:pdf,docx,doc'
                ]
            );

            if ($fileValidator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => $fileValidator->errors()->first(),
                    'title' => 'Validation Error',
                    'type' => 'error'
                ]);
            }
            //process file name and upload file
            $this->processAttachments($uploadedFile, $request->taskId);

            return response()->json([
                'success' => true,
                'message' => 'Attachments added successfully',
                'title' => 'Saved!',
                'type' => 'success'
            ]);
        }
    }

    /**
     * Deletes a task record from the database.
     *
     * This function checks if the task has any attachments before deleting the record.
     * If the task has attachments, it returns a JSON response indicating failure.
     * If the task does not have attachments, it deletes the task record and returns a JSON response indicating success.
     *
     * @param Request $request The incoming request containing the task ID.
     * @return \Illuminate\Http\JsonResponse The JSON response indicating the success or failure of the deletion operation.
     *
     * @author Hansana Sandeepa <hansana.s.somarathna@gmail.com>
     */
    public function deleteTask(Request $request)
    {
        $taskId = $request->id;
        Log::info($request);
        $task = Task::find($taskId);
        $hasAttachments = $task->attachments()->exists();

        if ($hasAttachments) {
            return response()->json(['success' => false]);
        } else {
            Task::destroy($taskId);
            return response()->json(['success' => true]);
        }
    }
    /**
     * Processes attachments related to a task.
     *
     * This function handles the process of uploading and storing attachments related to a task.
     * It generates unique names for each attachment, stores them in the specified directory, and
     * creates corresponding records in the database.
     *
     * @param Illuminate\Http\UploadedFile[] $attachments The uploaded attachments to be processed.
     * @param int $taskId The ID of the task to which the attachments belong.
     *
     * @return void
     *
     * @author Hansana Sandeepa <hansana.s.somarathna@gmail.com>
     */
    private function processAttachments($attachments, int $taskId)
    {
        if ($attachments) {
            foreach ($attachments as $key => $attachment) {
                $originalName = pathinfo($attachment->getClientOriginalName(), PATHINFO_FILENAME); //name without extension
                $attachmentName = $originalName . '-' . $key . '-' . uniqid() . '.' . $attachment->getClientOriginalExtension();

                // Move file to public/attachments folder
                $attachment->move(public_path('attachments'), $attachmentName);

                $attachmentData = [
                    'task_id' => $taskId,
                    'attachment' => $attachmentName,
                    'path' => 'attachments/' . $attachmentName, // store relative path for DB
                ];

                Attachment::create($attachmentData);
                Log::info("attachment created: " . $attachmentName);
            }
        }
    }


    public function getTasksByEvent(Request $request)
    {
        echo '<pre>';
        print_r($request);
        exit;
        $id = $request->id;
        $task = Task::find($id);
        return response()->json($task);
    }

    public function viewAttachment($id)
    {
        $attachment = Attachment::findOrFail($id);

        $absolutePath = public_path($attachment->path);

        if (!file_exists($absolutePath)) {
            abort(404, 'File not found');
        }

        return response()->file($absolutePath);
    }
}
