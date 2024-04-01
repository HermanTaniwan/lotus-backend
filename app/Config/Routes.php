<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');
$routes->get('/ingredients', 'Home::loadIngredients');
$routes->get('/recipe', 'Home::loadRecipe');
$routes->get('/search-recipe', 'Home::searchRecipe');
