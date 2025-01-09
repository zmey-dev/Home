<?php

namespace App\Http\Controllers;

use App\Models\Record;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RecordController extends Controller
{
    public function index(Request $request)
    {
        $index = $request->input("index", 0);

        $records = Record::skip($index * 10)->take(10)->get();

        return response()->json($records, 200);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string',
            'image' => 'nullable|image|max:2048',
            'category' => 'required|string|exists:categories,name',
        ]);

        $record = auth()->user()->records()->create([
            'name' => $data['name'],
            'image' => $data['image'] ? $data['image']->store('records') : null,
            'category' => $data['category'],
        ]);

        return response()->json($record, 201);
    }

    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'name' => 'required|string',
            'image' => 'nullable|image|max:2048',
            'category' => 'required|string|exists:categories,name',
        ]);

        $record = Record::findOrFail($id);

        if ($record->user_id != Auth::id())
            return response()->json("This is not your record", 403);
        $record->name = $data['name'];
        $record->category = $data['category'];

        if ($request->hasFile('image')) {
            $record->image = $data['image']->store('records');
        }

        $record->save();

        return response()->json($record, 200);
    }

    public function admin_delete(Request $request, $id)
    {
        $record = Record::find($id);

        if (!$record) {
            return response()->json(['message' => 'Record not found.'], 404);
        }

        if ($record->image && file_exists(storage_path('app/' . $record->image))) {
            unlink(storage_path('app/' . $record->image));
        }

        $record->delete();

        return response()->json(['message' => 'Record deleted successfully.'], 200);
    }

    public function user_delete(Request $request, $id)
    {
        $record = Record::find($id);

        if (!$record) {
            return response()->json(['message' => 'Record not found.'], 404);
        }

        if ($record->user_id != Auth::id())
            return response()->json("This is not your record", 403);

        if ($record->image && file_exists(storage_path('app/' . $record->image))) {
            unlink(storage_path('app/' . $record->image));
        }

        $record->delete();

        return response()->json(['message' => 'Record deleted successfully.'], 200);
    }


}
