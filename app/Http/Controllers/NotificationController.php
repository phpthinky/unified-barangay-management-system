<?php
namespace App\Http\Controllers;

class NotificationController
{
    public function __invoke()
    {
        $notifications = auth()->user()->notifications()->latest()->get();
        return view('notifications.index', compact('notifications'));
    }
}
