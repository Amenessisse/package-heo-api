<?php

namespace Amenessisse\PackageHeoAPI;

use Exception;
use InvalidArgumentException;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;
use Amenessisse\PackageHeoAPI\QueryBuilder\ProductQueryBuilder;

class HeoApiClient
{
    const HEO_BASE_URI = "https://integrate.heo.com/retailer-api/";
    const HEO_BASE_URI_TEST = "https://integrate.heo.com/retailer-api-test/";
    const HEO_BASE_URI_VERSION = 'v1';
    const HEO_CATALOG = '/catalog';
    const HEO_COLLECTIONS = '/collections';
    const HEO_COLLECTIONS_TYPES = '/collections/types';
    const HEO_COLLECTIONS_THEMES = '/collections/themes';
    const HEO_COLLECTIONS_SPECIALS = '/collections/specials';
    const HEO_COLLECTIONS_MANUFACTURERS = '/collections/manufacturers';
    const HEO_COLLECTIONS_CATEGORIES = '/collections/categories';
    const HEO_PRODUCTS = '/catalog/products';
    const HEO_PRICES = '/catalog/prices';
    const HEO_AVAILABILITIES = '/catalog/availabilities';

    // Formats de réponse supportés
    const FORMAT_JSON = 'application/json';
    const FORMAT_CSV = 'text/csv; charset=utf-8';

    /** @var string */
    private $username;
    /** @var string */
    private $password;
    /** @var HttpClientInterface */
    private $httpClient;
    /** @var string */
    private $urlAPI;

   /**
    * @param string $username Identifiant d'API
    * @param string $password Mot de passe d'API
    * @param bool $sandbox Utiliser l'environnement de test (true) ou de production (false)
    */
    public function __construct(string $username, string $password, bool $sandbox = false)
    {
        $this->httpClient = HttpClient::create();
        $this->username   = $username;
        $this->password   = $password;
        $this->urlAPI     = ($sandbox ? self::HEO_BASE_URI_TEST : self::HEO_BASE_URI) . self::HEO_BASE_URI_VERSION;
    }

    /**
     * Effectue une requête GET vers l'API en ajoutant les query parameters éventuels.
     *
     * @throws Exception
     */
    private function get(string $uri, array $query = [], string $format = self::FORMAT_JSON): ResponseInterface
    {
        $queryString = http_build_query($query, '', '&', PHP_QUERY_RFC3986);

        if (isset($query['query'])) {
            $queryString = preg_replace_callback('/query=([^&]+)/', function () use ($query) {
                return 'query=' . $query['query'];
            }, $queryString);
        }

        $url = $this->urlAPI . $uri . ($queryString ? '?' . $queryString : '');

        try {
            return $this->httpClient->request('GET', $url, [
                'headers' => ['Accept' => $format],
                'auth_basic' => [$this->username, $this->password],
            ]);
        } catch (TransportExceptionInterface $e) {
            throw new Exception($e->getMessage());
        }
    }

    /** @throws Exception */
    public function getCollectionsAll(): ResponseInterface
    {
        return $this->get(self::HEO_COLLECTIONS);
    }

    /** @throws Exception */
    public function getCollectionsTypes(): ResponseInterface
    {
        return $this->get(self::HEO_COLLECTIONS_TYPES);
    }

    /** @throws Exception */
    public function getCollectionsThemes(): ResponseInterface
    {
        return $this->get(self::HEO_COLLECTIONS_THEMES);
    }

    /** @throws Exception */
    public function getCollectionsSpecials(): ResponseInterface
    {
        return $this->get(self::HEO_COLLECTIONS_SPECIALS);
    }

    /** @throws Exception */
    public function getCollectionsManufacturers(): ResponseInterface
    {
        return $this->get(self::HEO_COLLECTIONS_MANUFACTURERS);
    }

    /** @throws Exception */
    public function getCollectionsCategories(): ResponseInterface
    {
        return $this->get(self::HEO_COLLECTIONS_CATEGORIES);
    }

    /** @throws Exception */
    public function getCatalog(): ResponseInterface
    {
        return $this->get(self::HEO_CATALOG);
    }

    /** @throws Exception */
    public function getCatalogCsv(): ResponseInterface
    {
        return $this->get(self::HEO_CATALOG, [], self::FORMAT_CSV);
    }

    /**
     * Récupère les produits.
     *
     * @param string|ProductQueryBuilder|null $query Une chaîne de query ou un ProductQueryBuilder pour guider l'utilisateur.
     * @param int $page Numéro de page (défaut : 1)
     * @param int $pageSize Nombre d'éléments par page (défaut : 10)
     *
     * @throws Exception
     */
    public function getProducts($query = null, int $page = 1, int $pageSize = 10): ResponseInterface
    {
        $params = $this->prepareQueryParams($query, $page, $pageSize);

        return $this->get(self::HEO_PRODUCTS, $params);
    }

    /**
     * Récupère les prix des produits.
     *
     * @param string|ProductQueryBuilder|null $query
     * @param int $page
     * @param int $pageSize
     *
     * @return ResponseInterface
     * @throws Exception
     */
    public function getPrices($query = null, int $page = 1, int $pageSize = 10): ResponseInterface
    {
        $params = $this->prepareQueryParams($query, $page, $pageSize);

        return $this->get(self::HEO_PRICES, $params);
    }

    /**
     * Récupère les disponibilités des produits.
     *
     * @param string|ProductQueryBuilder|null $query
     * @param int $page
     * @param int $pageSize
     *
     * @return ResponseInterface
     * @throws Exception
     */
    public function getAvailabilities($query = null, int $page = 1, int $pageSize = 10): ResponseInterface
    {
        $params = $this->prepareQueryParams($query, $page, $pageSize);

        return $this->get(self::HEO_AVAILABILITIES, $params);
    }

    /**
     * Prépare les paramètres de query pour l'appel à l'API.
     *
     * @param string|ProductQueryBuilder|null $query     Une chaîne de requête ou un objet ProductQueryBuilder
     * @param int $page                                  Numéro de page demandé (démarrant à 1)
     * @param int $pageSize                              Nombre d'éléments par page (entre 1 et 100)
     *
     * @throws InvalidArgumentException                 Si les paramètres de pagination sont invalides
     * @return array<string, mixed>                      Tableau des paramètres formatés pour l'API
     */
    private function prepareQueryParams($query, int $page, int $pageSize): array
    {
        if ($page < 1) {
            throw new InvalidArgumentException("Le numéro de page doit être supérieur ou égal à 1, '$page' fourni");
        }
        
        if ($pageSize < 1 || $pageSize > 100) {
            throw new InvalidArgumentException("La taille de page doit être comprise entre 1 et 100, '$pageSize' fourni");
        }
        
        $queryString = '';
        if ($query instanceof ProductQueryBuilder) {
            $queryString = $query->build();
        } elseif (is_string($query)) {
            $queryString = $query;
        } elseif ($query !== null) {
            throw new InvalidArgumentException(sprintf(
                "Le paramètre query doit être une chaîne, un ProductQueryBuilder ou null, %s fourni",
                gettype($query)
            ));
        }
    
        $params = [];
        if ($queryString !== '') {
            $params['query'] = $queryString;
        }

        $params['page']     = $page;
        $params['pageSize'] = $pageSize;

        return $params;
    }

    public function setSandboxMode(bool $sandbox): self
    {
        $this->urlAPI = ($sandbox ? self::HEO_BASE_URI_TEST : self::HEO_BASE_URI) . self::HEO_BASE_URI_VERSION;

        return $this;
    }

    public function getUrlAPI(): string
    {
        return $this->urlAPI;
    }

    /**
     * Valide la connexion en jouant une requête vers l'API.
     *
     * @return bool
     * @throws Exception
     * @throws TransportExceptionInterface
     * */
    public function isValidConnection(): bool
    {
        $response = $this->getProducts(null, 1, 1);

        return $response->getStatusCode() === 200;
    }
}