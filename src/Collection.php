<?php

namespace Weble\RevisoApi;

use Psr\Http\Message\UriInterface;

class Collection extends \Illuminate\Support\Collection
{
    protected string $keyName;
    protected object $info;

    public static function create(Client $client, object $info, string $keyName): static
    {
        return (new static((array) ($info->collection ?? [])))
            ->withInfo($info)
            ->map(fn ($item) => new Model($client, $keyName, $item))
            ->keyBy(function (Model $item) use ($keyName){
                $key = $item->getData()->get($keyName);
                if (!is_object($key)){
                    return $key;
                } elseif (method_exists($key, '__toString')) {
                    return (string) $key;
                } elseif (property_exists($key, "code")) {
                    return $key->code;
                } else {
                    return $key;
                }
            });
    }

    public function getMetadata(): ?object
    {
        return $this->info->metadata;
    }

    public function getPagination(): ?object
    {
        return $this->info->pagination;
    }

    public function getUrl(): UriInterface
    {
        return Client::createUri($this->info->self);
    }

    protected function withInfo(object $info): static
    {
        $this->info = $info;

        return $this;
    }
}
