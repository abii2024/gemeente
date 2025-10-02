<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreComplaintRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Allow anyone to submit complaints
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255|min:5',
            'description' => 'required|string|max:2000|min:10',
            'category' => 'required|string|in:wegen,openbare_verlichting,afval,groen,overlast,openbare_ruimte,water,overig',
            'priority' => 'nullable|string|in:low,medium,high,urgent',
            'location' => 'nullable|string|max:500',
            'lat' => 'nullable|numeric|between:-90,90',
            'lng' => 'nullable|numeric|between:-180,180',
            'reporter_name' => 'required|string|max:255|min:2|regex:/^[a-zA-Z\s\-\.\']+$/',
            'reporter_email' => 'required|email:rfc,dns|max:255',
            'reporter_phone' => 'nullable|string|max:20|regex:/^[\+\-\(\)\s\d]+$/',
            'attachments' => 'nullable|array|max:5',
            'attachments.*' => 'file|mimes:jpeg,jpg,png,gif,pdf|max:10240', // 10MB per file
        ];
    }

    /**
     * Get custom error messages.
     */
    public function messages(): array
    {
        return [
            'title.required' => 'Titel is verplicht.',
            'title.min' => 'Titel moet minimaal 5 tekens bevatten.',
            'description.required' => 'Beschrijving is verplicht.',
            'description.min' => 'Beschrijving moet minimaal 10 tekens bevatten.',
            'category.required' => 'Categorie is verplicht.',
            'category.in' => 'Selecteer een geldige categorie.',
            'priority.in' => 'Selecteer een geldige prioriteit.',
            'location.max' => 'Locatie mag maximaal 500 tekens bevatten.',
            'lat.between' => 'Latitude moet tussen -90 en 90 liggen.',
            'lng.between' => 'Longitude moet tussen -180 en 180 liggen.',
            'reporter_name.required' => 'Naam is verplicht.',
            'reporter_name.min' => 'Naam moet minimaal 2 tekens bevatten.',
            'reporter_name.regex' => 'Naam mag alleen letters, spaties, koppeltekens en punten bevatten.',
            'reporter_email.required' => 'E-mailadres is verplicht.',
            'reporter_email.email' => 'Voer een geldig e-mailadres in.',
            'reporter_phone.regex' => 'Voer een geldig telefoonnummer in.',
            'attachments.max' => 'Maximaal 5 bestanden toegestaan.',
            'attachments.*.max' => 'Elk bestand mag maximaal 10MB zijn.',
            'attachments.*.mimes' => 'Alleen JPEG, PNG, GIF en PDF bestanden zijn toegestaan.',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Sanitize input data
        $this->merge([
            'title' => $this->sanitizeString($this->title),
            'description' => $this->sanitizeString($this->description),
            'location' => $this->sanitizeString($this->location),
            'reporter_name' => $this->sanitizeString($this->reporter_name),
            'reporter_email' => strtolower(trim($this->reporter_email)),
            'priority' => $this->priority ?: 'medium',
        ]);
    }

    /**
     * Sanitize string input.
     */
    private function sanitizeString(?string $value): ?string
    {
        if (! $value) {
            return null;
        }

        // Remove extra whitespace and trim
        $value = preg_replace('/\s+/', ' ', trim($value));

        // Remove potentially dangerous characters but keep basic punctuation
        $value = preg_replace('/[<>{}\\\\]/', '', $value);

        return $value;
    }
}
