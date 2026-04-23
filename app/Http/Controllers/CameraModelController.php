<?php

namespace App\Http\Controllers;

use App\Models\CameraModel;
use Illuminate\Http\Request;

class CameraModelController extends Controller
{
    public function index()
    {
        $models = CameraModel::latest()->paginate(10);
        return view('camera-models.index', compact('models'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'model_name' => 'required|string|max:255',
        ]);

        // Check if a model with the same name exists (including trashed)
        $existing = CameraModel::withTrashed()->where('model_name', $request->model_name)->first();

        if ($existing) {
            if ($existing->trashed()) {
                $existing->restore();
                return redirect()->route('camera-models.index')->with('success', 'Camera model restored from trash successfully.');
            }

            return back()->withErrors(['model_name' => 'The model name has already been taken.'])->withInput();
        }

        CameraModel::create($request->all());

        return redirect()->route('camera-models.index')->with('success', 'Camera model created successfully.');
    }

    public function update(Request $request, CameraModel $cameraModel)
    {
        $request->validate([
            'model_name' => 'required|string|max:255|unique:camera_models,model_name,' . $cameraModel->id,
        ]);

        $cameraModel->update($request->all());

        return redirect()->route('camera-models.index')->with('success', 'Camera model updated successfully.');
    }

    public function destroy(CameraModel $cameraModel)
    {
        $cameraModel->delete();

        return redirect()->route('camera-models.index')->with('success', 'Camera model deleted successfully.');
    }
}
