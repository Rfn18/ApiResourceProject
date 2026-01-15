<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ItemResource;
use App\Models\Invoice;
use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class InvoiceController extends Controller
{
    public function index()
    {
        return new ItemResource(true, 'List Data Invoice!', Invoice::all());
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'no_nota' => 'required|string',
            'tanggal' => 'required|date',
            'total_harga' => 'required|numeric',
            'items' => 'required|array',
            'items.*.kode' => 'required|exists:items,kode',
            'items.*.jumlah' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
            ], 422);
        }

        $invoice = Invoice::create([
            'no_nota' => $request->no_nota,
            'tanggal' => $request->tanggal,
            'total_harga' => $request->total_harga,
        ]);

        foreach ($request->items as $item) {
            $itemData = Item::where('kode', $item['kode'])->firstOrFail();

            $invoice->items()->attach($itemData->id, [
                'jumlah' => $item['jumlah'],
                'total_harga' => $itemData->harga * $item['jumlah'],
            ]);
        }

        return new ItemResource(true, 'Data invoice berhasil ditambahkan!', $invoice);
    }

    public function show($id)
    {
        $invoice = Invoice::with('items')->find($id);

        if(!$invoice) {
            return new ItemResource(false, 'Id tidak ditemukan', $invoice);
        }

        return new ItemResource(true, 'List data invoice bedasarkan id', $invoice);
    }
}
