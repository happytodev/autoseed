<?php
namespace Happytodev\Autoseed\Tools;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;


class DatabaseTables
{
    public $options;

    public $excludedTables;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->options = [
            'connection' => '',
            'table'      => '',
            'folder'     => app()->path(),
            'debug'      => false,
            'all'        => false,
            'singular'   => '',
        ];

        $this->excludedTables = [
            "migrations",
            "failed_jobs",
            "password_resets"
        ];
    }

    public function describeTable($tableName)
    {
        // $this->doComment('Retrieving column information for : '.$tableName);

        if (strlen($this->options['connection']) <= 0) {
            return DB::select(DB::raw("describe `{$tableName}`"));
        } else {
            return DB::connection($this->options['connection'])->select(DB::raw("describe `{$tableName}`"));
        }
    }

    /**
     * will return an array of all table names.
     */
    public function getAllTables($excludedFlag = true)
    {
       ///ddd($this->options);
        $tables = [];

        if (strlen($this->options['connection']) <= 0) {
            $tables = collect(DB::select(DB::raw("show full tables where Table_Type = 'BASE TABLE'")))->flatten();
        } else {
            $tables = collect(DB::connection($this->options['connection'])->select(DB::raw("show full tables where Table_Type = 'BASE TABLE'")))->flatten();
        }

        $tables = $tables->map(function ($value, $key) {
            return collect($value)->flatten()[0];
        })->reject(function ($value, $key) {
            //return $value == 'migrations';
            return in_array($value, $this->excludedTables);
        });

        // remove excluded tables if flag is true


        return $tables;
    }

    public function getSchema($tableName)
    {
        // $this->doComment('Retrieving table definition for: '.$tableName);

        if (strlen($this->options['connection']) <= 0) {
            return Schema::getColumnListing($tableName);
        } else {
            return Schema::connection($this->options['connection'])->getColumnListing($tableName);
        }
    }

    public function getColumnType($table, $column)
    {
        if (strlen($this->options['connection']) <= 0) {
            //ddd(Schema::class);
            return Schema::getColumnType($table, $column);
        } else {
            return Schema::connection($this->options['connection'])->getColumnType($table, $column);
        }
    }

    // Return the excluded tables
    public function getExcludedTables()
    {
        return $this->excludedTables;
    }
}
