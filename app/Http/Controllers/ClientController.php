<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Client;
use App\Http\Controllers\Controller;
use App\Http\Requests\RequestClient;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Database\Eloquent\Builder;
use Laravel\Passport\ClientRepository;
use Laravel\Passport\Http\Controllers\AccessTokenController;
use GuzzleHttp\Client as GuzzleClient;
use Psr\Http\Message\ServerRequestInterface;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BaseController as BaseController;

class ClientController extends BaseController
{
    /**
     * Instantiate a new controller instance.
     *
     * @return void
     */
    public function __construct(ClientRepository $clients)
    {
        $this->middleware('auth:api')->except('checkSecret');

        $this->middleware('guest')->only('checkSecret');

        $this->clients = $clients;
    }

    /**
     * Pantalla inicial listado de clientes
     *
     * @return View
     */
    public function index()
    {
        return Client::all();
    }

    /**
     * Pantalla inicial listado de clientes
     *
     * @return View
     */
    public function info()
    {
        return view('reports.feed_info');
    }

    /**
     * Retorna los clientes del usuario autenticado
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return App\Client
     */
    public function show(Request $request)
    {
        $user = Auth::user();

        $clients = Client::getActivos($user->id)->paginate(20);

        return [
            'pagination' => [
                'total' => $clients->total(),
                'current_page' => $clients->currentPage(),
                'per_page' => $clients->perPage(),
                'last_page' => $clients->lastPage(),
                'from' => $clients->firstItem(),
                'to' => $clients->lastPage(),
            ],
            'clients' => $clients
        ];
    }

    /**
     * Metodo de creacion de clientes
     *
     * * @param  App\Http\Requests\RequestClient  $request
     * @return \Illuminate\Http\Response|\Laravel\Passport\Client
     */
    public function store(Request $request)
    {
        $clientInstance = new Client();
        $client = $clientInstance->createClient(
            $request->user()->getKey(),
            $request->name,
            $request->redirect,
            $request->api_id
        )->makeVisible('secret');

        return $client;
    }

    /**
     * Metodo para actualizar un cliente dado
     *
     * @param  App\Http\Requests\RequestClient $request
     * @param  string  $clientId
     * @return \Illuminate\Http\Response|\Laravel\Passport\Client
     */
    public function update(RequestClient $request, $clientId)
    {
        $client = $this->clients->findForUser($clientId, $request->user()->getKey());

        if (!$client) {
            return new Response('', 404);
        }

        $client =  $this->clients->update(
            $client,
            $request->name,
            $request->redirect,
            $request->api_id
        );

        return $client;
    }

    /**
     * Visualizacion por cliente del numero total de feeds
     *  en un periodo de tiempo, tipos de feed y fecha
     *  y hora del ultimo feed realizado
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return App\Client
     */
    public function clientWithInfo(Request $request)
    {
        $user = Auth::user();

        $now = new DateTime('now');

        $minDate = $request->minDate ? $request->minDate : $now;

        $maxDate = $request->maxDate ? $request->maxDate : $now;

        $clientId = $request->client_id;

        if ($request->client_id) {

            $clients = Client::where('revoked', 0)->where('user_id', '=', $user->id)->where('id', $clientId)->withCount(['activities' => function (Builder $query) use ($minDate, $maxDate) {
                $query->whereDate('created_at', '<=', $maxDate);
                $query->whereDate('created_at', '>=', $minDate);
            }])->paginate(20);
        } else {

            $clients = Client::where('revoked', 0)->where('user_id', '=', $user->id)->withCount(['activities' => function (Builder $query) use ($minDate, $maxDate) {
                $query->whereDate('created_at', '<=', $maxDate);
                $query->whereDate('created_at', '>=', $minDate);
            }])->paginate(20);
        }

        return [
            'pagination' => [
                'total' => $clients->total(),
                'current_page' => $clients->currentPage(),
                'per_page' => $clients->perPage(),
                'last_page' => $clients->lastPage(),
                'from' => $clients->firstItem(),
                'to' => $clients->lastPage(),
            ],
            'clients' => $clients
        ];
    }

    public function destroy(Client $client)
    {
        $client->delete();

        return $this->sendResponse([], 'Client deleted succesfully!');
    }

    public function checkSecret(Request $request)
    {
        $client = Client::where('secret', $request->headers->get('apiKey'))->first();

        if (empty($client)) {
            return $this->sendError('Invalid credentials', ['error' => 'Unauthorized'], 401);
        }

        $success['authenticated'] = true;
        $success['client'] = $client;

        return $this->sendResponse($success, 'Valid api key!');
    }
}
