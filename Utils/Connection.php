<?php

namespace BeeDelivery\GoogleMaps\Utils;

use Illuminate\Support\Facades\Http;

class Connection
{
    protected $key;

    /*
     * Pega valores no arquivo de configuração do pacote e atribui às variáveis
     * para utilização na classe.
     *
     * @return void
     */
    public function __construct()
    {
        $this->key = config('googlemaps.key');
    }

    /*
     * Realiza uma solicitação get padrão utilizando
     * Bearer Authentication.
     *
     * @param string $url
     * @param array|null $params
     * @return array
     */
    public function get($url)
    {
        try {
            $url = $url . '&key=' . $this->key;
            $response = Http::withHeaders([
                'Accept' => 'application/json'
            ])->get($url);

            return  json_decode($response->getBody(), true);
        } catch (\Exception $e) {
            return [
                'code' => $e->getCode(),
                'response' => $e->getMessage()
            ];
        }
    }

    /*
     * Realiza uma solicitação post padrão utilizando
     * Bearer Authentication.
     *
     * @param string $url
     * @param array $data
     * @return array
     */
    public function post(string $url, array $data, string $fieldMask = '*')
    {
        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'X-Goog-Api-Key' => $this->key,
                'X-Goog-FieldMask' => $fieldMask,
            ])->post($url, $data);

            return json_decode($response->getBody(), true);
        } catch (\Exception $e) {
            return [
                'code' => $e->getCode(),
                'response' => $e->getMessage()
            ];
        }
    }
}
