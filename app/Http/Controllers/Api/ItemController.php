<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ItemResource;
use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ItemController extends Controller
{
    public function index()
    {
        return new ItemResource(true, 'List data items!', Item::all());
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'kode' => 'required|string',
            'nama_barang' => 'required|string',
            'harga' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors(),
            ], 422);
        }

        $item = Item::create($request->all());

        return new ItemResource(true, 'Berhasil input data item!', $item);
    }

    public function show($id)
    {
        $items = Item::find($id);

        return new ItemResource(true, "ist data item bedasarkan id", $items);
    }

    public function update(Request $request, $id)
    {
        $item = Item::findOrFail($id);
        $item->update($request->all());

        return response()->json($item);
    }

    public function destroy($id)
    {
        Item::destroy($id);

        return response()->json(null, 204);
    }
}
