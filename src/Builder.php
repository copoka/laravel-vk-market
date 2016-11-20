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

    private $access_token;
    private $expires_in;
    private $user_id;

    private $config = [];
    // private $app_id;
    // private $api_secret;


    public function __construct(Application $application)
    {
        $this->config = $application->make('config')->get('vk-market');
        // $this->app_id = $this->config['app_id'];
        // $this->api_secret = $this->config['api_secret'];
        $this->client = new Client();
    }

    public function isAuth(): bool
    {
        return $this->access_token !== null;
    }

    public function authorize(Request $request)
    {
        // $request->has('code')
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
        $this->expires_in = $response['expires_in'];
        $this->user_id = $response['user_id'];

        return $response;
    }

    public function setAccessToken(string $access_token)
    {
        $this->access_token = $access_token;
    }

    public function marketGet(int $count = 100, int $offset = 0, int $album_id = 0)
    {
        $response = $this->getMethod('market.get', [
            'owner_id' => '-30426745',
            'count'    => $count,
            'offset'   => $offset,
            'album_id' => $album_id,
        ]);

        unset($response['response'][0]);

        return $response['response'];
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

    public function marketGetCategories(int $count = 100, int $offset = 0)
    {
        $response = $this->getMethod('market.getCategories', [
            'count'  => $count,
            'offset' => $offset,
        ]);

        unset($response['response'][0]);

        return $response['response'];
    }
}
