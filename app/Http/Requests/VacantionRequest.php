<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;

class VacantionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(Request $request)
    {
        $rules = [
            'leave_type_id' => 'required',
            'forward_user_id' => 'required',
            'from_date' => 'required',
            'to_date' => 'required',
            'status' => 'required',
        ];

        if ($request->leave_type_id == 7) {
            $rules['reason'] ='required|max:255';
        }

        return $rules;
    }

    public function messages()
    {
        return [
            'leave_type_id.required' => "Leave Type is required",
            'forward_user_id.required' => "Forwarad User is required",
        ];
    }
}
