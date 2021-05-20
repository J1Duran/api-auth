<?php

namespace App;

use App\Models\Passport\AuthCode;
use App\Models\Passport\PersonalAccessClient;
use App\Models\Passport\Token;
use Illuminate\Support\Str;

// use Illuminate\Database\Eloquent\Model;
use Laravel\Passport\Client as PassportClient;

class Client extends PassportClient
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'api_id', 'name', 'redirect'
    ];

    /**
     * Store a new client.
     *
     * @param  int  $userId
     * @param  string  $name
     * @param  string  $redirect
     * @param  string|null  $provider
     * @param  bool  $personalAccess
     * @param  bool  $password
     * @param  bool  $confidential
     * @return \Laravel\Passport\Client
     */
    public function createClient($userId, $name, $redirect, $api_id, $provider = null, $personalAccess = false, $password = false, $confidential = true)
    {

        $client = $this->forceFill([
            'user_id' => $userId,
            'name' => $name,
            'secret' => ($confidential || $personalAccess) ? Str::random(40) : null,
            'provider' => $provider,
            'redirect' => $redirect,
            'api_id' => $api_id,
            'personal_access_client' => $personalAccess,
            'password_client' => $password,
            'revoked' => false,
        ]);

        $client->save();

        return $client;
    }
}
