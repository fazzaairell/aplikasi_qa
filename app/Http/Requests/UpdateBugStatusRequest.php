<?php

namespace App\Http\Requests;

use App\Models\Bug;
use Illuminate\Foundation\Http\FormRequest;

class UpdateBugStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Role dasar (admin/qa lead/qa tester/developer) sudah dicek middleware di route.
        // Aturan "developer hanya boleh ubah bug miliknya sendiri" & alur transisi status
        // butuh data $bug itu sendiri, jadi tetap diproses di BugStatusService, bukan di sini.
        return true;
    }

    public function rules(): array
    {
        return [
            'status'         => ['required', 'in:' . implode(',', Bug::STATUSES)],
            'fix_attachment' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
        ];
    }
}
