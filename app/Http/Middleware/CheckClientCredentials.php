<?php

namespace App\Http\Middleware;

use Illuminate\Auth\AuthenticationException;
use Laravel\Passport\TokenRepository;
use Laravel\Passport\Http\Middleware\CheckClientCredentials as PassportClientCredentials;
use League\OAuth2\Server\Exception\OAuthServerException;
use League\OAuth2\Server\ResourceServer;
use Symfony\Bridge\PsrHttpMessage\Factory\PsrHttpFactory;
use Zend\Diactoros\ResponseFactory;
use Zend\Diactoros\ServerRequestFactory;
use Zend\Diactoros\StreamFactory;
use Zend\Diactoros\UploadedFileFactory;
use Closure;

class CheckClientCredentials extends PassportClientCredentials
{
    public function __construct(ResourceServer $server, TokenRepository $repository)
    {
        parent::__construct($server, $repository);
    }

    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @param mixed ...$scopes
     * @return mixed
     * @throws AuthenticationException
     * @throws \Laravel\Passport\Exceptions\MissingScopeException
     */
    public function handle($request, Closure $next, ...$scopes)
    {
        $psr = (new PsrHttpFactory(
            new ServerRequestFactory,
            new StreamFactory,
            new UploadedFileFactory,
            new ResponseFactory
        ))->createRequest($request);
        
        try {
            $psr = $this->server->validateAuthenticatedRequest($psr);
            $request->request->set('client_id', $psr->getAttribute('oauth_client_id'));
				//$request->get('client_id');
        } catch (OAuthServerException $e) {
            throw new AuthenticationException;
        }
        $this->validate($psr, $scopes);
        return $next($request);
    }
}
