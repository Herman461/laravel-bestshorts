<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserEditRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'fullname' => 'nullable|min:3|max:50',
            'avatar' => 'nullable|image|mimes:jpg,jpeg,png|min:200|max:10240',
            'description' => 'nullable|min:6|max:1024',
            'x' => 'nullable|max:2048|starts_with:x.com/',
            'instagram' => 'nullable|max:2048|starts_with:instagram.com/',
            'reddit' => 'nullable|max:2048|starts_with:reddit.com/',
            'website' => 'nullable|max:2048'
        ];
    }
}
