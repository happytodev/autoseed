<?php

Route::get('autoseed', function() {
    echo "Hello from Autoseed";
});


Route::get('autoseed/list', 'Happytodev\Autoseed\DbController@index');
