<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\ValidationRule;

/**
 * @method array<string,mixed> validated()
 */
class StoreTrackerRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required',
            'icon' => 'required',
            'unit' => 'nullable',
            'value_step' => 'required|numeric|min:0',
            'default_value' => 'required|numeric',
            'target_value' => 'required|numeric',
            'target_score' => 'required|numeric',
        ];
    }
}
