<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBugRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Otorisasi role untuk route ini sudah ditangani oleh middleware di routes/web.php.
        return true;
    }

    public function rules(): array
    {
        return [
            'title'           => 'required|string|max:255',
            'description'     => 'required|string',
            'expected_result' => 'nullable|string',
            'assigned_to'     => 'required|exists:users,id',
            'due_date'        => 'required|date',
            'attachment'      => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            
        ];
    }
}
