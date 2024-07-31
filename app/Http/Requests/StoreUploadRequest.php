<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreUploadRequest extends FormRequest
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
            'excel_file' => 'required|file|mimes:xls,xlsx,csv'
        ];
    }
    public function messages()
    {
        return [
            'excel_file.required' => 'Por favor, selecione um arquivo com extensão: xls, xlsx ou csv para upload.',
            'excel_file.mimes' => 'O arquivo deve ter uma extensão válida: xls, xlsx ou csv.',

        ];
    }
}
