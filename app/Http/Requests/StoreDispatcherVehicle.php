<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDispatcherVehicle extends FormRequest
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
            'vehicle_id' => "required|unique:dispatcher_vehicles,vehicle_id,".$this->request->get('dispatcher_vehicle_id'),
            'route_id' => 'required',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'vehicle_id.required' => __('A vehicle is required'),
            'vehicle_id.unique' => __('The vehicle :other has been register'),
            'route_id.required'  => __('A route is required'),
        ];
    }
}
