<?php

namespace App\Http\Requests;


class EventRequest extends Request
{
    protected $checkboxFields = [
        'automate',
    ];

    protected $dateFields = [
        'start',
    ];

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|string',
            'start' => 'required|date_format:"'.$this->dateFormat.'"',
            'drivers_per_heat' => 'required|integer',
            'heats_per_driver' => 'required|integer',
            'drivers_per_final' => 'required|integer',
            'advance_per_final' => 'required|integer',
            'laps_per_heat' => 'required|integer',
            'laps_per_final' => 'required|integer',
            'car_model' => 'required|string',
            'points_sequence_id' => 'required|exists:points_sequences,id'
        ];
    }
}
