<?php

declare(strict_types = 1);

namespace Vlsoprun\VkMarket;

use GuzzleHttp\Client;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\Request;

class Builder
{
    const API_URL_METHOD = 'https://api.vk.com/method/';
    const API_URL_AUTHORIZE = 'https://oauth.vk.com/authorize';
    const API_URL_ACCESS_TOKEN = 'https://oauth.vk.com/access_token';

    const API_VERSION = '5.60';

    protected $access_token;
    protected $config = [];

    public function __construct(Application $application)
    {
        $this->config = $application->make('config')->get('vk-market');
        $this->client = new Client();
    }

    public function isAuth(): bool
    {
        return $this->access_token !== null;
    }

    public function getAuthorizeUrl(): string
    {
        $params = [
            'client_id'    => $this->config['app_id'],
            'display'      => 'page',
            'scope'        => 'market,photos',
            'redirect_uri' => $this->config['redirect_uri'],
        ];

        return self::API_URL_AUTHORIZE . '?' . http_build_query($params);
    }

    public function getAccessToken(string $code)
    {
        $params = [
            'client_id'     => $this->config['app_id'],
            'client_secret' => $this->config['api_secret'],
            'redirect_uri'  => $this->config['redirect_uri'],
            'code'          => $code,
        ];

        $url = self::API_URL_ACCESS_TOKEN . '?' . http_build_query($params);

        $response = $this->client->get($url);
        $response = $response->getBody()->getContents();
        $response = json_decode($response, true);

        $this->access_token = $response['access_token'];

        return $response;
    }

    public function setAccessToken(string $access_token)
    {
        $this->access_token = $access_token;
    }

    public function getMethod(string $method, array $params): array
    {
        $params = array_merge($params, [
            'access_token' => $this->access_token,
        ]);

        $url = self::API_URL_METHOD . $method . '?' . http_build_query($params);

        $response = $this->client->get($url);
        $content = $response->getBody()->getContents();

        return json_decode($content, true);
    }
}
