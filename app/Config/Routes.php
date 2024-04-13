<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');
$routes->get('/ingredients', 'Home::loadIngredients');
$routes->get('/recipe', 'Home::loadRecipe');
$routes->get('/search-recipe', 'Home::searchRecipe');
$routes->get('/search-recipe-nlp', 'Home::searchRecipeNLP');
$routes->get('/scrape-youtube', 'Scrape::scrapeYT');
$routes->get('/all-ingredients', 'RecipeLibraryController::getIngredient');
$routes->get('/all-recipe-tags', 'RecipeLibraryController::getRecipeTags');
$routes->get('/recipe-library', 'RecipeLibraryController::index');
$routes->get('/recipe-editor', 'RecipeLibraryController::loadEditor');
$routes->get('/recipe-published', 'RecipeLibraryController::getPublishedRecipe');
$routes->post('/recipe-submit', 'RecipeLibraryController::submitRecipe');
$routes->post('/recipe-youtube-submit', 'RecipeLibraryController::submitRecipeYT');
$routes->post('/update-recipe-tags', 'RecipeLibraryController::updateRecipeTags');
