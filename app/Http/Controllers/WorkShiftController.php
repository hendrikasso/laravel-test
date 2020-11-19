<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\WorkShift;

class WorkShiftController extends Controller
{
    public function list()
    {
        return response()->json(WorkShift::all()->toArray(), 200);
    }
 
    public function show(WorkShift $workShift)
    {
        return response()->json($workShift, 200);
    }

    public function create(Request $request)
    {
        $validatedData = $request->validate([
            'name' => ['required', 'max:80', 'unique:work_shifts'],
            'begin' => ['required', 'regex:/^([0-1]?[0-9]|2[0-3]):(00|30)$/'],
            'end' => ['required', 'regex:/^([0-1]?[0-9]|2[0-3]):(00|30)$/'],
        ]);

        $workShift = WorkShift::create($request->all());

        return response()->json($workShift, 201);
    }

    public function update(Request $request, WorkShift $workShift)
    {
        $validatedData = $request->validate([
            'name' => ['required', 'max:80'],
            'begin' => ['required', 'regex:/^([0-1]?[0-9]|2[0-3]):(00|30)$/'],
            'end' => ['required', 'regex:/^([0-1]?[0-9]|2[0-3]):(00|30)$/'],
        ]);

        $workShift->update($request->all());

        return response()->json($workShift, 200);
    }

    public function delete(Request $request, WorkShift $workShift)
    {
        $workShift->delete();

        return response()->json(null, 204);
    }
}
