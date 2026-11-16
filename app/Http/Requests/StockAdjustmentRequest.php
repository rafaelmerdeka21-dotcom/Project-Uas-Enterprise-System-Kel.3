<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StockAdjustmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->isAdmin();
    }

    public function rules(): array
    {
        return [
            'product_id' => ['required', 'exists:products,id'],
            'physical_stock' => ['required', 'integer', 'min:0'],
            'reason' => ['required', 'string', 'min:3', 'max:500'],
        ];
    }

    public function messages(): array
    {
        return [
            'product_id.required' => 'Produk barang wajib dipilih.',
            'physical_stock.required' => 'Jumlah stok fisik aktual wajib diisi.',
            'reason.required' => 'Alasan penyesuaian (audit reason) wajib diisi untuk jejak kepatuhan.',
            'reason.min' => 'Alasan penyesuaian minimal 3 karakter.',
        ];
    }
}
