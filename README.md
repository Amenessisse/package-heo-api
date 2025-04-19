# Documentation du Package Heo API Client

## Introduction

Le package `amenessisse/package-heo-api` est un client PHP pour l'API Heo, permettant d'accéder facilement aux ressources de catalogue, collections et produits via une interface simple et intuitive.

## Installation

Installez le package via Composer :

```shell script
composer require amenessisse/package-heo-api
```


## Configuration requise

- PHP 7.0 ou supérieur
- Symfony HTTP Client 5.0 ou supérieur

## Utilisation de base

### Initialisation du client

```php
<?php

use Amenessisse\PackageHeoAPI\HeoApiClient;

// Initialisation avec vos identifiants
$client = new HeoApiClient('votre_nom_utilisateur', 'votre_mot_de_passe');
```


### Accès aux collections

```php
// Récupérer toutes les collections
$collections = $client->getCollectionsAll()->toArray();

// Récupérer les types de collections
$types = $client->getCollectionsTypes()->toArray();

// Autres méthodes disponibles
$themes = $client->getCollectionsThemes()->toArray();
$specials = $client->getCollectionsSpecials()->toArray();
$manufacturers = $client->getCollectionsManufacturers()->toArray();
$categories = $client->getCollectionsCategories()->toArray();
```


### Accès au catalogue

```php
// Récupérer le catalogue complet
$catalog = $client->getCatalog()->toArray();
```


### Accès aux produits

#### Avec des requêtes simples

```php
// Récupérer les produits (page 1, 50 éléments par page)
$products = $client->getProducts(null, 1, 50)->toArray();

// Requête avec une chaîne de filtre simple
$productsFiltered = $client->getProducts("ageRating==1", 1, 50)->toArray();
```


#### Avec le constructeur de requêtes

```php
<?php

use Amenessisse\PackageHeoAPI\QueryBuilder\ProductQueryBuilder;

// Création d'une requête complexe
$queryBuilder = (new ProductQueryBuilder())
    ->where('ageRating', '==', 1)
    ->orWhere('ageRating', '==', 7);

// Utilisation dans la requête de produits
$products = $client->getProducts($queryBuilder, 1, 50)->toArray();
```


### Accès aux prix et disponibilités

```php
// Récupérer les prix des produits
$prices = $client->getPrices($queryBuilder, 1, 50)->toArray();

// Récupérer les disponibilités des produits
$availabilities = $client->getAvailabilities($queryBuilder, 1, 50)->toArray();
```


## Constructeur de requêtes (ProductQueryBuilder)

Le constructeur de requêtes permet de créer des filtres complexes pour vos requêtes API.

### Opérateurs disponibles

- `==` : égalité
- `!=` : différence
- `=lt=` : inférieur à
- `=gt=` : supérieur à
- `=in=` : appartient à

### Méthodes

#### where()

Ajoute une condition de filtre.

```php
$queryBuilder->where('field', 'operator', 'value');
```


#### orWhere()

Ajoute une condition alternative (OR).

```php
$queryBuilder->where('field1', '==', 'value1')
             ->orWhere('field2', '==', 'value2');
// Génère: field1==value1 or field2==value2
```


#### build()

Génère la chaîne de requête finale.

```php
$queryString = $queryBuilder->build();
```


## Gestion des erreurs

Toutes les méthodes de l'API peuvent lancer des exceptions en cas d'erreur :

```php
<?php

use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

try {
    $response = $client->getProducts("ageRating==1", 1, 50);
    $data = $response->toArray();
} catch (Exception $e) {
    // Gestion des erreurs générales
    echo "Erreur : " . $e->getMessage();
} catch (TransportExceptionInterface $e) {
    // Gestion des erreurs de transport HTTP
    echo "Erreur de communication avec l'API : " . $e->getMessage();
}
```


## Constants et endpoints disponibles

Le client expose plusieurs constantes définissant les endpoints de l'API :

- `HEO_BASE_URI` : URI de base de l'API
- `HEO_CATALOG` : Endpoint du catalogue
- `HEO_COLLECTIONS` : Endpoint des collections
- `HEO_PRODUCTS` : Endpoint des produits
- `HEO_PRICES` : Endpoint des prix
- `HEO_AVAILABILITIES` : Endpoint des disponibilités

## Notes

- Toutes les méthodes de l'API retournent un objet `ResponseInterface` de Symfony HTTP Client
- Utilisez la méthode `toArray()` sur la réponse pour obtenir les données au format tableau
- Le package utilise la version de test de l'API Heo par défaut (`https://integrate.heo.com/retailer-api-test/`)

## Licence

Ce package est distribué sous licence MIT.
