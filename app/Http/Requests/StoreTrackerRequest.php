<?php

namespace App\Http\Requests;

use Closure;
use App\Models\User;
use App\Models\Category;
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
        /** @var User $user */
        $user = $this->user();

        return [
            'category_id' => [
                'nullable',
                'numeric',
                'exists:App\Models\Category,id',
                function (string $attribute, int $value, Closure $fail) use ($user) {
                    if (Category::find($value)?->user?->id !== $user->id) {
                        $fail("The {$attribute} is invalid.");
                    }
                },
            ],
            'name' => 'required',
            'icon' => 'required',
            'order' => 'numeric',
            'unit' => 'nullable',
            'value_step' => 'required|numeric|min:0',
            'target_value' => 'required|numeric|min:0.001',
            'target_score' => 'required|numeric',
            'single' => 'required|boolean',
            'overflow' => 'required|boolean'
        ];
    }
}
