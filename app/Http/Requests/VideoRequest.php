<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VideoRequest extends FormRequest
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
            'sort' => 'in:newly-released,top-this-month,most-viewed,oldest',
            'type' => 'in:grid,feed',
            'page' => 'integer|min:1',
            'seed' => 'string',
            'username' => 'string',
            'tag' => 'string'
        ];
    }
}
