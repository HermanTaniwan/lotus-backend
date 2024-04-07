<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');
$routes->get('/ingredients', 'Home::loadIngredients');
$routes->get('/recipe', 'Home::loadRecipe');
$routes->get('/search-recipe', 'Home::searchRecipe');
$routes->get('/scrape-youtube', 'Home::scrapeYT');
$routes->get('/all-ingredients', 'RecipeLibraryController::getIngredient');
$routes->get('/recipe-library', 'RecipeLibraryController::index');
$routes->get('/recipe-editor', 'RecipeLibraryController::loadEditor');
$routes->post('/recipe-submit', 'RecipeLibraryController::submitRecipe');
