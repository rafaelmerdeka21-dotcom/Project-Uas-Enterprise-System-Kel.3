<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PurchaseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->isAdmin();
    }

    public function rules(): array
    {
        return [
            'supplier_id' => ['required', 'exists:suppliers,id'],
            'purchase_date' => ['required', 'date'],
            'status' => ['required', 'in:pending,received,canceled'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'exists:products,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'items.*.unit_price' => ['required', 'numeric', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'supplier_id.required' => 'Pemasok wajib dipilih.',
            'purchase_date.required' => 'Tanggal pembelian wajib diisi.',
            'items.required' => 'Minimal satu barang harus ditambahkan.',
            'items.min' => 'Minimal satu barang harus ditambahkan.',
            'items.*.product_id.required' => 'Barang wajib dipilih.',
            'items.*.quantity.min' => 'Jumlah barang minimal 1.',
        ];
    }
}
