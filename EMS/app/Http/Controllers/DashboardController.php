<?php

namespace App\Http\Controllers;

use App\Models\NewEvent;


class DashboardController extends Controller
{
    public function index()
    {
        $new_event = NewEvent::where('active_status', 'running')
            ->orderBy('created_at', 'ASC') //new code store in asc. 
            ->get();

        return view('admin.dashboard', compact('new_event'));
    }

    /*public function index() //old code with active status
    {
        $new_event = NewEvent::where('active_status', 'running')->orderBy('start_date', 'ASC')->get();

        return view('admin.dashboard', compact('new_event'));

    }*/
}
