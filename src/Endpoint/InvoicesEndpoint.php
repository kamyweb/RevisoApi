<?php

namespace Weble\RevisoApi\Endpoint;

use Illuminate\Support\Collection;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Message\UriInterface;
use Weble\RevisoApi\Client;
use Weble\RevisoApi\Exceptions\ErrorResponseException;

class InvoicesEndpoint extends Endpoint
{
    protected string $subType = '';

    public function __construct(Client $client, UriInterface $uri)
    {
        $this->client = $client;
        $this->uri = $uri;
    }
    
    public function subType(string $subType): static
    {
        $this->subType = $subType;

        $this->listEndpoint = new ListEndpoint($this->client, $this->getListRoute(), $this->getResourceKey()??'');

        return $this;
    }

    public function getResourceKey(): string|int|null
    {
        if ($this->subType==='drafts'){
            return 'id';
        }
        return $this->getRouteParameters($this->getFindRoute())->first() ?? null;
    }

    public function getFindRoute(): UriInterface
    {
        if ($this->subType==='overdue' || $this->subType==='paid' || $this->subType==='unpaid'){
            return Client::createUri($this->getRouteListByType('booked')->where('method', 'GET')->slice(1,1)->first()?->path ?? '');
        }
        return Client::createUri($this->getRouteList()->where('method', 'GET')->slice(1,1)->first()?->path ?? '');
    }

    public function getRouteList(): Collection
    {
        if (empty($this->subType)) {
            $routes = (new Collection($this->getInfo()->routes))
                ->map(function ($route) {
                    $route->path = $this->cleanRouteParameters(Client::createUri($route->path));

                    return $route;
                });

            // First route on projects is not good
            $routes->shift();
        }else{
            $routes = $this->getRouteListByType($this->subType);
        }

        return $routes;
    }
    
    

    /**
     * @throws ErrorResponseException
     * @throws ClientExceptionInterface
     */
    private function getRouteListByType(string $subType): Collection
    {
        $routes = (new Collection($this->getInfo()->routes))
            ->map(function ($route) {
                $route->path = $this->cleanRouteParameters(Client::createUri($route->path));

                return $route;
            });
        $baseRoute = $routes->shift();
        $basePath = $baseRoute?->path?->getPath() ?? '';
        $routesByType = [];
        foreach ($routes as $route){
            $type = explode('/', str_replace($basePath, '', $route?->path->getPath()))[1] ?? null;
            if ($type === $subType) {
                $routesByType[] = $route;
            }
        }

        // reorder by method and path to get a consistent order of GET and POST the single invoice
        usort($routesByType, function($a, $b){
            if ($a->method !== $b->method){
                $pos = array_flip(['GET', 'POST', 'PUT', 'DELETE', 'PATCH']);
                return $pos[$a->method] <=> $pos[$b->method];
            }
            return $a->path->getPath() <=> $b->path->getPath();
        });
        
        return (new Collection($routesByType));
    }
}
