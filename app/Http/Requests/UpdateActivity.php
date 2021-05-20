<?php

namespace App\Http\Requests;

use App\Activity;
use App\Client;
use Illuminate\Foundation\Http\FormRequest;

class UpdateActivity extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $activity = Activity::where('stream_id', $this->route('activityId'))->first();

        $client = Client::find($this->get('client_id'));

        $clientActivity = $activity->streamUser->client;

        if ($clientActivity->id === $client->id) {
            return true;
        }

        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            // 'id' => 'required|integer|exists:activities,id',
            // 'verb' => 'required|max:10',
            // 'place' => 'max:255',
            'message' => 'string',
            // 'object' => 'required|string',
            'participants' => 'array',
            'coordinates' => 'array',
            'coordinates.lat' => 'string',
            'coordinates.long' => 'string',
            'images.*' => 'mimes:jpg,jpeg,png|max:50000',
        ];
    }
}
