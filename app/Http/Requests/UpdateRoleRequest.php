<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Spatie\Permission\Models\Permission;

class UpdateRoleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255', 'unique:roles,name,'.$this->role->id],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['string', 'in:'.Permission::pluck('name')->implode(',')],
            'users' => ['nullable', 'array'],
            'users.*' => ['exists:users,id'],
        ];
    }
}
