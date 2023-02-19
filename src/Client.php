<?php

namespace Equip;

use Exception;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Psr7\Response;

class Client
{
    const LANG_RU = 'ru';
    const LANG_EN = 'en';

    const CATALOG_TYPE_ANY = -1;
    const CATALOG_TYPE_ALL = 0;
    const CATALOG_TYPE_MAIN = 1;
    const CATALOG_TYPE_ACCESSORIES = 2;
    const CATALOG_TYPE_MINOR = 3;

    private string $apikey;
    private string $host;
    private GuzzleClient $client;

    public function __construct(string $apikey, string $host = 'https://api.equip.me/v1/')
    {
        $this->apikey = $apikey;
        $this->host = $host;
        $this->client = new GuzzleClient(
            [
                'base_uri' => $this->host,
                'timeout' => 45.0,
                'http_errors' => false,
            ]
        );
    }

    /**
     * @throws GuzzleException|ResponseErrorException
     * @throws Exception
     */
    public function legalEntities(string $lang = self::LANG_RU): array
    {
        $resp = $this->client->get($this->queryApikey('dealer/inn', ['lang' => $lang]));
        return $this->responseHandler($resp);
    }

    /**
     * @throws GuzzleException|ResponseErrorException
     * @throws Exception
     */
    public function stocks(string $lang = self::LANG_RU): array
    {
        $resp = $this->client->get($this->queryApikey('dealer/stocks', ['lang' => $lang]));
        return $this->responseHandler($resp);
    }

    /**
     * @throws GuzzleException|ResponseErrorException
     * @throws Exception
     */
    public function productCategories(int $catalogTypeId, string $lang = self::LANG_RU): array
    {
        $params = [
            'lang' => $lang,
            'catalog_type_id' => $catalogTypeId,
        ];
        $resp = $this->client->get($this->queryApikey('dealer/products/categories', $params));
        return $this->responseHandler($resp);
    }

    /**
     * @throws GuzzleException|ResponseErrorException
     * @throws Exception
     */
    public function products(ProductsFilter $filter): array
    {
        $resp = $this->client->get($this->queryApikey('dealer/products', $filter->toParams()));
        return $this->responseHandler($resp);
    }

    /**
     * @throws GuzzleException|ResponseErrorException
     * @throws Exception
     */
    public function productsByVendorCode(array $vendorCodes, bool $withRRC = false, bool $withCategory = false, string $inn = '', string $lang = self::LANG_RU): array
    {
        $resp = $this->client->post('dealer/products', [
            'json' => $this->jsonApikey([
                'products' => $vendorCodes,
                'with_rrc' => $withRRC,
                'with_category' => $withCategory,
                'inn' => $inn,
                'lang' => $lang,
            ]),
        ]);
        return $this->responseHandler($resp);
    }

    /**
     * @throws GuzzleException|ResponseErrorException
     * @throws Exception
     */
    public function productsByVendorCodeShort(array $vendorCodes, bool $withRRC = false, string $stock = '', string $inn = '', string $lang = self::LANG_RU): array
    {
        $resp = $this->client->post('dealer/products', [
            'json' => $this->jsonApikey([
                'products' => $vendorCodes,
                'with_rrc' => $withRRC,
                'stock' => $stock,
                'inn' => $inn,
                'lang' => $lang,
            ]),
        ]);
        return $this->responseHandler($resp);
    }

    /**
     * @throws GuzzleException|ResponseErrorException
     * @throws Exception
     */
    public function productInfo(string $code1C, bool $withRRC = false, string $inn = '', string $lang = self::LANG_RU): array
    {
        $params = [
            'lang' => $lang,
            'inn' => $inn,
            'with_rrc' => $withRRC,
        ];
        $resp = $this->client->get($this->queryApikey('dealer/product/' . urlencode($code1C), $params));
        return $this->responseHandler($resp);
    }

    private function queryApikey(string $url, array $params = []): string
    {
        return $url . '?' . http_build_query($this->canonicalParams($params));
    }

    private function jsonApikey(array $params = []): array
    {
        return [
            'request' => $this->canonicalParams($params),
        ];
    }

    private function canonicalParams(array $params): array
    {
        if (isset($params['catalog_type_id']) && $params['catalog_type_id'] < 0) {
            $params['catalog_type_id'] = '';
        }
        if (isset($params['with_rrc'])) {
            $params['with_rrc'] = $params['with_rrc'] ? 'true' : 'false';
        }

        $params['apikey'] = $this->apikey;
        return $params;
    }

    /**
     * @throws ResponseErrorException
     * @throws Exception
     */
    private function responseHandler(Response $resp): array
    {
        $content = $resp->getBody()->getContents();

        switch ($resp->getStatusCode()) {
            case 200:
                return json_decode($content, true)['response'] ?? [];

            case 500:
                $err = json_decode($content, true);
                $content = empty($err['response']) ? $content : 'Server Error';
                throw new ResponseErrorException($content, $resp->getStatusCode());

            default:
                $err = json_decode($content, true);
                if (empty($err['response'])) {
                    throw new Exception($content, $resp->getStatusCode());
                }

                throw new ResponseErrorException($err['response']['error_message'], (int)$err['response']['error_code']);
        }
    }
}