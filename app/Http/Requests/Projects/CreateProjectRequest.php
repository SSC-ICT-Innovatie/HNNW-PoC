<?php

namespace App\Http\Requests\Projects;

use Auth;
use Illuminate\Foundation\Http\FormRequest;

class CreateProjectRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return Auth::check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            "project_status_id" => "required|exists:project_statuses,id",
            "project_category_id" => "required|exists:project_categories,id",
            "work_method_id" => "required|exists:work_methods,id",
            "title" => "required",
            "slogan" => "nullable",
            "description" => "required",
            "starts_at" => "nullable",
            "ends_at" => "nullable",
            "header_image" => "nullable|image",
            "resources" => "nullable",
            "team_roles" => "nullable",
        ];
    }
}