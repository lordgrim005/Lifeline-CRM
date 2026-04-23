<?php

namespace App\Http\Controllers;

use App\Models\Camera;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $totalCameras = Camera::count();
        $availableCameras = Camera::where('status', 'Available')->count();
        $bookedCameras = Camera::where('status', 'Booked')->count();
        $rentedCameras = Camera::where('status', 'Rented')->count();

        return view('dashboard', compact(
            'totalCameras',
            'availableCameras',
            'bookedCameras',
            'rentedCameras'
        ));
    }
}
