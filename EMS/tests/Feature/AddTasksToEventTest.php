<?php

namespace Tests\Feature;

use App\Models\EventInstanceTask;
use App\Models\NewEvent;
use App\Models\Task;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class AddTasksToEventTest extends TestCase
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
    }

    public function test_it_adds_selected_catalog_tasks_to_an_existing_event_only_once(): void
    {
        $event = $this->createEvent();
        $task = $this->createTask($event->event_type_id);

        $this->post(route('space_event.tasks.store', $event->id), [
            'tasks' => [$task->id],
        ])->assertRedirect(route('space_event.runnigEvents', $event->id));

        $this->post(route('space_event.tasks.store', $event->id), [
            'tasks' => [$task->id],
        ])->assertRedirect(route('space_event.runnigEvents', $event->id));

        $this->assertSame(
            1,
            EventInstanceTask::where('new_event_id', $event->id)
                ->where('tasks_id', $task->id)
                ->count()
        );
        $this->assertDatabaseHas('event_instance_tasks', [
            'new_event_id' => $event->id,
            'tasks_id' => $task->id,
            'status' => 'Todo',
        ]);
    }

    public function test_it_rejects_tasks_from_a_different_event_type(): void
    {
        $event = $this->createEvent();
        $task = $this->createTask($event->event_type_id + 1);

        $this->from(route('space_event.runnigEvents', $event->id))
            ->post(route('space_event.tasks.store', $event->id), [
                'tasks' => [$task->id],
            ])
            ->assertRedirect(route('space_event.runnigEvents', $event->id))
            ->assertSessionHasErrors('tasks.0');

        $this->assertDatabaseCount('event_instance_tasks', 0);
    }

    private function createEvent(): NewEvent
    {
        return NewEvent::create([
            'event_type_id' => 1,
            'event_name' => 'Water Rocket Competition 2026',
            'start_date' => '2026-12-01',
            'end_date' => '2026-12-02',
            'coordinator' => '1',
            'active_status' => 'running',
        ]);
    }

    private function createTask(int $eventTypeId): Task
    {
        return Task::create([
            'task_name' => 'Prepare launch area',
            'event_type_id' => $eventTypeId,
        ]);
    }
}
