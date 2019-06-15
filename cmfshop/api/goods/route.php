<?php

use think\Route;

Route::resource('goods/categories', 'goods/Categories');
Route::get('goods/categories/subCategories', 'goods/Categories/subCategories');
Route::resource('goods/articles', 'goods/Articles');
Route::resource('goods/pages', 'goods/Pages');
Route::resource('goods/userArticles', 'goods/UserArticles');

Route::get('goods/search', 'goods/Articles/search');
Route::get('goods/articles/my', 'goods/Articles/my');
Route::get('goods/articles/relatedArticles', 'goods/Articles/relatedArticles');
Route::post('goods/articles/doLike', 'goods/Articles/doLike');
Route::post('goods/articles/cancelLike', 'goods/Articles/cancelLike');
Route::post('goods/articles/doFavorite', 'goods/Articles/doFavorite');
Route::post('goods/articles/cancelFavorite', 'goods/Articles/cancelFavorite');
Route::get('goods/tags/:id/articles', 'goods/Tags/articles');
Route::get('goods/tags', 'goods/Tags/index');
Route::get('goods/tags/hotTags', 'goods/Tags/hotTags');

Route::post('goods/userArticles/deletes', 'goods/UserArticles/deletes');