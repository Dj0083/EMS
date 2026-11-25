<?php

namespace Tests\Feature;

use App\Models\AssignedEmployee;
use App\Models\EventInstanceTask;
use App\Models\InstanceTasksAssign;
use App\Models\Task;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class DeleteTaskFromEventListsTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config([
            'database.default' => 'sqlite',
            'database.connections.sqlite.database' => ':memory:',
            'database.connections.sqlite.foreign_key_constraints' => false,
        ]);
        DB::purge('sqlite');

        Schema::connection('sqlite')->create('tasks', function (Blueprint $table) {
            $table->id();
            $table->string('task_name');
            $table->unsignedBigInteger('event_type_id');
            $table->timestamps();
        });

        Schema::connection('sqlite')->create('event_instance_tasks', function (Blueprint $table) {
            $table->id();
            $table->string('status');
            $table->unsignedBigInteger('tasks_id');
            $table->unsignedBigInteger('new_event_id');
            $table->timestamps();
        });

        Schema::connection('sqlite')->create('instance_tasks_assign', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('event_instance_task_id');
            $table->unsignedBigInteger('assigned_emp_id');
            $table->string('status');
            $table->timestamps();
        });

        Schema::connection('sqlite')->create('assigned_emp', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('task_id');
            $table->unsignedBigInteger('emp_id');
            $table->timestamps();
        });

        Schema::connection('sqlite')->create('attachments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('task_id');
            $table->string('attachment')->nullable();
            $table->string('path')->nullable();
            $table->timestamps();
        });
    }

    public function test_deleting_a_catalogue_task_removes_it_from_all_event_lists(): void
    {
        $task = Task::create([
            'task_name' => 'Prepare launch area',
            'event_type_id' => 1,
        ]);
        $otherTask = Task::create([
            'task_name' => 'Register participants',
            'event_type_id' => 1,
        ]);

        $firstYearTask = $this->createEventTask($task->id, 2025);
        $secondYearTask = $this->createEventTask($task->id, 2026);
        $otherEventTask = $this->createEventTask($otherTask->id, 2026);

        InstanceTasksAssign::create([
            'event_instance_task_id' => $firstYearTask->id,
            'assigned_emp_id' => 1,
            'status' => 'Done',
        ]);
        InstanceTasksAssign::create([
            'event_instance_task_id' => $secondYearTask->id,
            'assigned_emp_id' => 2,
            'status' => 'Done',
        ]);
        InstanceTasksAssign::create([
            'event_instance_task_id' => $otherEventTask->id,
            'assigned_emp_id' => 3,
            'status' => 'Done',
        ]);
        AssignedEmployee::create(['task_id' => $task->id, 'emp_id' => 1]);

        $this->deleteJson(route('task.deleteTask'), ['id' => $task->id])
            ->assertOk()
            ->assertJson(['success' => true]);

        $this->assertDatabaseMissing('tasks', ['id' => $task->id]);
        $this->assertDatabaseMissing('event_instance_tasks', ['id' => $firstYearTask->id]);
        $this->assertDatabaseMissing('event_instance_tasks', ['id' => $secondYearTask->id]);
        $this->assertDatabaseMissing('instance_tasks_assign', [
            'event_instance_task_id' => $firstYearTask->id,
        ]);
        $this->assertDatabaseMissing('instance_tasks_assign', [
            'event_instance_task_id' => $secondYearTask->id,
        ]);
        $this->assertDatabaseMissing('assigned_emp', ['task_id' => $task->id]);
        $this->assertDatabaseHas('event_instance_tasks', ['id' => $otherEventTask->id]);
        $this->assertDatabaseHas('instance_tasks_assign', [
            'event_instance_task_id' => $otherEventTask->id,
        ]);
    }

    private function createEventTask(int $taskId, int $year): EventInstanceTask
    {
        return EventInstanceTask::create([
            'tasks_id' => $taskId,
            'new_event_id' => $year,
            'status' => 'Todo',
        ]);
    }
}
