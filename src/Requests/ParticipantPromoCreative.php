<?php

namespace Eventjuicer\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ParticipantPromoCreative extends FormRequest
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
    public function rules()
    {
        return [
           "name"               => "required|min:3|max:200",
           "data.title"         => "required|min:10|max:200",
           "data.description"   => "present",
           "template_id"        => "required|numeric"
        ];
    }

    public function messages()
    {
        return [
            
            "name"              => "Musisz podać nazwę kreacji",
            
            "data.title.required"   => "Musisz podać tytuł",
            "data.title.min"        => "Musisz podać dłuższy tytuł",
            "data.title.max"        => "Musisz podać krótszy tytuł",
            
            "data.description"   => "",
            "template_id"        => "Wybierz szablon"

        ];
    }

}
