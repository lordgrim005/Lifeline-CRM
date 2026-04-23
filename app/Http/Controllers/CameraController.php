<?php

namespace App\Http\Controllers;

use App\Models\Camera;
use App\Models\CameraModel;
use Illuminate\Http\Request;

class CameraController extends Controller
{
    public function index()
    {
        $cameras = Camera::with('model')->latest()->paginate(10);
        $models = CameraModel::all();
        return view('cameras.index', compact('cameras', 'models'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'model_id' => 'required|exists:camera_models,id',
            'serial_number' => 'required|string|unique:cameras,serial_number',
            'status' => 'required|string',
            'condition_notes' => 'nullable|string',
        ]);

        Camera::create($request->all());

        return redirect()->route('cameras.index')->with('success', 'Camera added successfully.');
    }

    public function update(Request $request, Camera $camera)
    {
        $request->validate([
            'model_id' => 'required|exists:camera_models,id',
            'serial_number' => 'required|string|unique:cameras,serial_number,' . $camera->id,
            'status' => 'required|string',
            'condition_notes' => 'nullable|string',
        ]);

        $camera->update($request->all());

        return redirect()->route('cameras.index')->with('success', 'Camera updated successfully.');
    }

    public function destroy(Camera $camera)
    {
        $camera->delete();
        return redirect()->route('cameras.index')->with('success', 'Camera removed to trash.');
    }
}
