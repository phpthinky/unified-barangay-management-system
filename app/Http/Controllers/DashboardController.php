<?php
namespace App\Http\Controllers;

class DashboardController
{
    public function __invoke()
    {
        return view('resident.dashboard');
    }
}
