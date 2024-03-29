<?php

namespace App\Http\Requests\Admin\Server;

use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
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
            'title' => 'required|string',
            'image' => 'nullable|file',
            'auth_ip' => 'required|string',
            'auth_port' => 'required|string',
            'game_ip' => 'required|string',
            'game_port' => 'required|integer',
            'game_console_port' => 'required|integer',
            'db_ip' => 'required|string',
            'db_port' => 'required|integer',
            'username' => 'required|string',
            'password' => 'required|string',
            'telecaster_db' => 'required|string|unique:servers,telecaster_db',
            'arcadia_db' => 'required|string|unique:servers,arcadia_db'
        ];
    }
}
