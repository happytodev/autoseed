<?php

namespace Happytodev\Autoseed;

use App\Http\Controllers\Controller;
use Happytodev\Autoseed\Tools\DatabaseTables;
// use Autoseed\Tools\DatabaseTables as ToolsDatabaseTables;
// use Tools\DatabaseTables;
use Illuminate\Http\Request;


class DbController extends Controller
{
    public function index()
    {
        $autoseed = new DatabaseTables();
        $result = $autoseed->getAllTables();
        foreach ($result as $table) {

            $fields = $autoseed->getSchema($table);

            //ddd($fields);
            foreach ($fields as $field) {

                // ddd($field);
                // getColumnType($table, $column)
                //ddd($autoseed, $table);
                $columnType = $autoseed->getColumnType($table, $field);
                print ($field . ' est de type ' . $columnType . '<br />');
            }

        }
    }
}
