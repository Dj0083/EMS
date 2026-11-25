<?php

namespace Tests\Feature;

use App\Models\AssignedEmployee;
use App\Models\EventInstanceTask;
use App\Models\InstanceTasksAssign;
use App\Models\NewEvent;
use App\Models\Task;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class UpdateTaskStatusTest extends TestCase
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

        Schema::connection('sqlite')->create('new_event', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('event_type_id');
            $table->string('event_name');
            $table->string('start_date');
            $table->string('end_date');
            $table->string('coordinator');
            $table->string('active_status');
            $table->timestamps();
        });

        Schema::connection('sqlite')->create('event_instance_tasks', function (Blueprint $table) {
            $table->id();
            $table->string('status');
            $table->unsignedBigInteger('tasks_id');
            $table->unsignedBigInteger('new_event_id');
            $table->timestamps();
        });

        Schema::connection('sqlite')->create('assigned_emp', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('task_id');
            $table->unsignedBigInteger('emp_id');
            $table->timestamps();
        });

        Schema::connection('sqlite')->create('instance_tasks_assign', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('event_instance_task_id');
            $table->unsignedBigInteger('assigned_emp_id');
            $table->string('status');
            $table->timestamps();
        });
    }

    public function test_task_status_tracks_assignee_completions_and_can_be_reopened(): void
    {
        $event = NewEvent::create([
            'event_type_id' => 1,
            'event_name' => 'Water Rocket Competition 2026',
            'start_date' => '2026-12-01',
            'end_date' => '2026-12-02',
            'coordinator' => '1',
            'active_status' => 'running',
        ]);
        $task = Task::create([
            'task_name' => 'Prepare launch area',
            'event_type_id' => 1,
        ]);
        $eventTask = EventInstanceTask::create([
            'tasks_id' => $task->id,
            'new_event_id' => $event->id,
            'status' => 'Todo',
        ]);
        $firstAssignee = AssignedEmployee::create([
            'task_id' => $task->id,
            'emp_id' => 10,
        ]);
        $secondAssignee = AssignedEmployee::create([
            'task_id' => $task->id,
            'emp_id' => 11,
        ]);

        $this->postJson(route('instance_task_assign.updateStatus'), [
            'event_instance_task_id' => $eventTask->id,
            'assigned_emp_id' => $firstAssignee->id,
            'completed' => true,
        ])->assertOk()->assertJson(['success' => true, 'status' => 'Todo']);

        $this->postJson(route('instance_task_assign.updateStatus'), [
            'event_instance_task_id' => $eventTask->id,
            'assigned_emp_id' => $secondAssignee->id,
            'completed' => true,
        ])->assertOk()->assertJson(['success' => true, 'status' => 'Done']);

        $this->postJson(route('instance_task_assign.updateStatus'), [
            'event_instance_task_id' => $eventTask->id,
            'assigned_emp_id' => $firstAssignee->id,
            'completed' => false,
        ])->assertOk()->assertJson(['success' => true, 'status' => 'Todo']);

        $this->assertDatabaseMissing('instance_tasks_assign', [
            'event_instance_task_id' => $eventTask->id,
            'assigned_emp_id' => $firstAssignee->id,
        ]);
        $this->assertDatabaseHas('instance_tasks_assign', [
            'event_instance_task_id' => $eventTask->id,
            'assigned_emp_id' => $secondAssignee->id,
        ]);
    }
}
