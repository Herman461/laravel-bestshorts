<?php

namespace App\Http\Requests;

use App\Models\Tag;
use Illuminate\Foundation\Http\FormRequest;

class VideoUploadRequest extends FormRequest
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
//            'session_id' => 'required|uuid',
            'from_sec' => 'required|numeric|max:4096',
            'to_sec' => 'required|numeric|max:4096',
//            'tags' => 'required|array|min:3|max:10', // Ensure it's an array with a minimum of 3 and a maximum of 10 elements
//            'tags.*' => 'required|integer|exists:tags,id',
            'video' => 'required|file|mimes:mp4,mov|max:204800', // 204800 КБ = 200 МБ
        ];
    }

    public function messages()
    {
        return [
            'tags.required' => 'Tags are required.',
            'tags.array' => 'Tags must be an array.',
            'tags.min' => 'At least 3 tags are required.',
            'tags.max' => 'A maximum of 10 tags is allowed.',
            'tags.*.required' => 'Each tag must be specified.',
            'tags.*.integer' => 'Tag ID must be an integer.',
            'tags.*.exists' => 'The specified tag does not exist.',
        ];
    }
}
