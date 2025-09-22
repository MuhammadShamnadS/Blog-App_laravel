<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;

class CategoryBulkImportRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'file' => 'required|file|mimes:csv,txt',
        ];
    }

    public function withValidator($validator)
{
    $validator->after(function ($validator) {
        if (!$this->hasFile('file')) return;

        $file = $this->file('file');
        $rows = array_map('str_getcsv', file($file->getRealPath()));

        // Remove header
        $header = array_shift($rows);

        foreach ($rows as $index => $row) {
            // Skip completely empty rows
            if (empty(array_filter($row))) continue;

            // Category validation
            $category = trim($row[0] ?? '');
            if (strlen($category) < 2) {
                $validator->errors()->add(
                    "row_{$index}_category",
                    "Category must be minimum 2 characters"
                );
            } elseif (!preg_match('/^[A-Za-z]+$/', $category)) {
                $validator->errors()->add(
                    "row_{$index}_category",
                    "Category must contain only alphabets"
                );
            }


            for ($i = 1; $i < count($row); $i++) {
                $tag = trim($row[$i] ?? '');
                if (empty($tag)) continue; 

                if (strlen($tag) < 2) {
                    $validator->errors()->add(
                        "row_{$index}_tag_{$i}",
                        "Tag must be minimum 2 characters"
                    );
                } elseif (!preg_match('/^[A-Za-z]+$/', $tag)) {
                    $validator->errors()->add(
                        "row_{$index}_tag_{$i}",
                        "Tag must contain only alphabets"
                    );
                }
            }
        }
    });
}


    protected function failedValidation(Validator $validator)
    {
        $response = response()->json([
            'message' => 'Validation failed',
            'errors' => $validator->errors()
        ], 422);

        throw new ValidationException($validator, $response);
    }

    public function messages()
    {
        return [
            'file.required' => 'Please choose a valid CSV file',
            'file.file' => 'A valid CSV file is required',
            'file.mimes' => 'Not a valid csv file',
        ];
    }
}
