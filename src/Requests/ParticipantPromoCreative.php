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
            
            "name.required"   => "Musisz podać nazwę",
            "name.min"        => "Musisz podać dłuższą nazwę",
            "name.max"        => "Musisz podać krótszą nazwę",
            
            "data.title.required"   => "Musisz podać tytuł",
            "data.title.min"        => "Musisz podać dłuższy tytuł",
            "data.title.max"        => "Musisz podać krótszy tytuł",
            
            "data.description.present"   => "Musisz podać opis",
     
            "template_id.required"       => "Wybierz szablon po prawej",
            "template_id.numeric"        => "Wybierz szablon po prawej",

        ];
    }

}
