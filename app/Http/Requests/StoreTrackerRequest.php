<?php

namespace App\Http\Requests;

use App\Models\Category;
use App\Models\User;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

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
                'required',
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
            'overflow' => 'required|boolean',
            'stale_delay' => 'nullable|numeric|min:1',
        ];
    }
}
