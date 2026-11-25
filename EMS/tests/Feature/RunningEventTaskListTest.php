<?php

namespace Tests\Feature;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class RunningEventTaskListTest extends TestCase
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

        Schema::connection('sqlite')->create('event_types', function (Blueprint $table) {
            $table->id();
            $table->string('event_type');
        });

        Schema::connection('sqlite')->create('employees', function (Blueprint $table) {
            $table->id();
            $table->string('emp_name');
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
    }

    public function test_orphaned_event_task_rows_are_not_shown_as_na(): void
    {
        DB::table('event_types')->insert([
            'id' => 1,
            'event_type' => 'Water Rocket Competition',
        ]);
        DB::table('new_event')->insert([
            'id' => 1,
            'event_type_id' => 1,
            'event_name' => 'Water Rocket Competition 2026',
            'start_date' => '2026-12-01',
            'end_date' => '2026-12-02',
            'coordinator' => '1',
            'active_status' => 'running',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('event_instance_tasks')->insert([
            'id' => 1,
            'status' => 'Todo',
            'tasks_id' => 999,
            'new_event_id' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->get(route('space_event.runnigEvents', 1))
            ->assertOk()
            ->assertSee('No tasks have been added to this event yet.')
            ->assertDontSee('N/A');
    }
}
