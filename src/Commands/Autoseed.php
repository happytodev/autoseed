<?php

namespace Happytodev\Autoseed\Commands;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use Happytodev\Autoseed\Tools\DatabaseTables;
use Illuminate\Console\GeneratorCommand;

class Autoseed extends GeneratorCommand
{

    /**
     *
     * @var array Fields not to match
     */
    public $immutableFields = [
        'id',
        'created_at',
        'updated_at'
    ];

    /**
     * Correspondence between mysql field type and faker data type
     */
    public $typeMySqlVsFaker = [
        'bigint(20) unsigned' => 'randomNumber',
        'varchar(255)' => 'paragraph',
        'timestamp' => 'dateTime',
        'varchar(100)' => 'sentence',
        'varchar(20)' => 'word',
        'varchar' => 'text'
    ];

    /**
     * Correspondence between mysql field name and faker data type
     */
    public $fieldsVsFaker = [
        'username' => 'name',
        'name' => 'name',
        'address' => 'address',
        'email' => 'email',
        'mail' => 'email',
        'email_verified_at' => 'dateTime',
        'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
        'remember_token' => ['fakerOperation' => 'shuffle', 'param' => 'dgxfqskjhl' ],
        'title' => ['fakerOperation' => 'text', 'param' => 50 ]
        ];

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'autoseed:generate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Launch automatic generation of factories and seeds based on your database structure. And populate your db with fake datas.';


    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

        $this->comment('Starting Database parsing...');


        // Instanciate database object
        $exploreDB = new DatabaseTables();

        $this->comment('==> Tables excluded : ' . implode(', ', $exploreDB->excludedTables));

        $tablesToParse = $exploreDB->getAllTables()->toArray();

        $this->comment('==> Tables to parse : ' .implode(',', $tablesToParse));

        foreach ($tablesToParse as $table)
        {

            $this->comment('==> Processing ' . $table . ' table.');

            // test if a factory is available, if not create it
            if ($this->isFileExists('database/factories/' . Str::studly(Str::singular($table)) . 'Factory.php'))
            {
                $this->comment('... Factory for table ' . $table . ' already exists.');
            } else
            {
                // copy the right stub in the factory directory
                $path = $this->writeTemplate($table, 'factory');

                // write data in the template just copied
                $this->fillTemplate($table, $path, 'factory');

                $this->comment('... Factory for table ' . $table . ' was created.');

            }


            // test if a seeder is available, if not create it
            if ($this->isFileExists('database/seeds/' . Str::studly(Str::singular($table)) . 'TableSeeder.php'))
            {
                $this->comment('... Seeder for table ' . $table . ' already exists.');
            } else
            {
                // copy the right stub in the factory directory
                $path = $this->writeTemplate($table, 'seeder');

                // write data in the template just copied
                $this->fillTemplate($table, $path, 'seeder');

                $this->comment('... Seeder for table ' . $table . ' was created.');

            }

            // Add Seeder to DatabaseSeeder.php
            $dbSeeder = file_get_contents('database/seeds/DatabaseSeeder.php');
            $studlyTable = Str::studly(Str::singular($table));
            $search = '// $this->call(UserSeeder::class);';
            $replace='// $this->call(UserSeeder::class);' . PHP_EOL . '$this->call(' . $studlyTable . 'TableSeeder::class);';
            $replaced = str_replace($search, $replace, $dbSeeder);
            $resultFpc = file_put_contents('database/seeds/DatabaseSeeder.php', $replaced);
            if ($resultFpc)
            {
                $this->comment('==> DatabaseSeeder writtten.');

            }
        }
        $this->comment('==> Dump autoload launched, it could be take few moment. Be patient...');
        $this->call('dump-autoload');
        $this->comment('==> Dump autoload finished !!!');


        // run php artisan db:seed
        $this->comment('==> Launch db:seed command.');

        $this->call('db:seed');
        $this->comment('All done. Enjoy your fakes datas ;-)');

        return 0;
    }


    /**
     * Check if a file exists. Use to determine if factory or seeds already exists before to create them
     *
     * @var $filenameWithPath file with complete path
     * @return bool
     */
    public function isFileExists($filenameWithPath)
    {
        if (File::exists($filenameWithPath))
        {
            return true;
        }

        return false;
    }


    /**
     * returns the factory stub to use to generate the class.
     */
    public function getFactoryStub()
    {
        return $this->getStub('factory');
    }

    /**
     * returns the seeder stub to use to generate the class.
     */
    public function getSeederStub()
    {
        return $this->getStub('seeder');
    }

    /**
     * Get the stub
     *
     * @param string $which choose factory or seeder
     * @return string path for the stub type selected
     */
    public function getStub($which=null)
    {
        return $which == 'factory'
                        ? __DIR__.'/../Stubs/factory.stub'
                        : __DIR__.'/../Stubs/seeder.stub';
    }

    /**
     * Write the Markdown template for the mailable.
     *
     * @param string $table table name
     * @param string $type factory or seeder
     * @return string $path path of the template just created
     */
    protected function writeTemplate($table, $type='factory')
    {
        // Singularize and studly for model Name
        $table = Str::studly(Str::singular($table));

        switch ($type) {
            case 'seeder':
                $this->comment('... loading seeder stub');
                $path = 'database/seeds/' . Str::studly($table) . 'TableSeeder.php';
                $this->files->put($path, file_get_contents($this->getSeederStub()));
                $this->comment('... seeder stub loaded');
            break;

            case 'factory':
                default:
                $this->comment('... loading factory stub');
                $path = 'database/factories/' . Str::studly($table) . 'Factory.php';
                $this->files->put($path, file_get_contents($this->getFactoryStub()));
                $this->comment('... factory stub loaded');
            break;
        }

        return $path;

    }

    protected function fillTemplate($table, $path, $type='factory')
    {
        switch ($type) {
            case 'factory':
                $this->fillTemplateFactory($table, $path);
            break;

            case 'seeder':
                $this->fillTemplateSeeder($table, $path);
            default:
                # code==>
                break;
        }

    }

    protected function fillTemplateSeeder($table, $path)
    {
        // getTemplateVariablesToFill()
        $variablesToFill =  $this->getTemplateVariablesToFill('seeder');

        foreach ($variablesToFill as $variable) {
            // fill the class variable
            if ($variable == 'class') {
                $this->fillClass($table, $path);
            }
        }
    }


    protected function fillTemplateFactory($table, $path)
    {
        // getTemplateVariablesToFill()
        $variablesToFill =  $this->getTemplateVariablesToFill('factory');

        foreach ($variablesToFill as $variable) {
            // fill the namespaceModel variable
            if ($variable == 'namespacedModel') {
                $this->fillNamespacedModel($table, $path);
            }
            if ($variable == 'model') {
                $this->fillModel($table, $path);
            }

            // fill the factoryContent variable
            if ($variable == 'factoryContent') {
                $this->fillFactoryContent($table, $path);
            }

        }
    }

    /**
     * Fill the factory content
     *
     * @todo optimized code
     *
     * @param string $table
     * @param string $path
     * @return void
     */
    protected function fillFactoryContent($table, $path)
    {
        $factoryContent = '';
        // get an instance of DatabaseTable
        $dbExplore = new DatabaseTables();

        $fields = $dbExplore->describeTable($table);
        foreach ($fields as $field) {
            // ignore immutable Fields (id, created_at, updated_at, etc.)
            if(in_array($field->Field, $this->immutableFields))
            {
                $this->comment('... Ignored field : ' . $field->Field);

                continue;
            }

            if ('password' == $field->Field)
            {
                $factoryContent .= "'" . $field->Field . "' => ";
            } else
            {
                $factoryContent .= "'" . $field->Field . "' => " . '$faker->';
            }

            if (array_key_exists($field->Field, $this->fieldsVsFaker))
            {
                if(!is_array($this->fieldsVsFaker[$field->Field]))
                {
                    if ('password' == $field->Field)
                    {
                        $factoryContent .= "'" . $this->fieldsVsFaker[$field->Field] . "'";
                    }
                    else {
                        $factoryContent .= $this->fieldsVsFaker[$field->Field];
                    }
                } else {
                    $factoryContent .= $this->fieldsVsFaker[$field->Field]['fakerOperation'] . "('" . $this->fieldsVsFaker[$field->Field]['param'] . "')";
                }
            } else {
                $factoryContent .= $this->typeMySqlVsFaker[$field->Type];
            }

            $factoryContent .= ',' . PHP_EOL;
        }

        $this->replaceVariableInStub($path, '{{ factoryContent }}', $factoryContent);
    }

    /**
     * Fill namespacedModel in Factory stub
     *
     * @param string $table database tables related
     * @param string $path path for the stub
     * @param string $basePath
     *
     * @return void
     */
    protected function fillNamespacedModel ($table, $path, $basePath='App')
    {
        $namespacedModel = $basePath . '\\' . Str::studly(Str::singular($table));

        $this->replaceVariableInStub($path, '{{ namespacedModel }}', $namespacedModel);

    }

    /**
     * Fill model in Factory stub
     *
     * @param string $table database tables related
     * @param string $path path for the stub
     *
     * @return void
     */
    protected function fillModel($table, $path)
    {
        $model = Str::studly(Str::singular($table));

        $this->replaceVariableInStub($path, '{{ model }}', $model);
    }

    /**
     * Fill class in Seeder stub
     *
     * @param string $table database tables related
     * @param string $path path for the stub
     *
     * @return void
     */
    protected function fillClass($table, $path)
    {
        $seederClass = Str::studly(Str::singular($table));

        $this->replaceVariableInStub($path, '{{ class }}', $seederClass);
    }

    /**
     * Generic function to replace a variable in a stub
     *
     * @param string $path Path to the stub to fill
     * @param string $search String to search
     * @param string $replace String for the replacement
     *
     * @return bool true if OK, false else.
     */
    protected function replaceVariableInStub($path, $search, $replace)
    {
        if (file_exists($path))
        {
            $stub = file_get_contents($path);
            $data = str_replace($search, $replace, $stub);
            if (file_put_contents($path, $data))
            {
                return true;
            }
            else {
                return false;
            }
        }
        else {
            return false;
        }
    }

    /**
     * Return the stubs variables to fill
     *
     * @param string $type type of stub ('factory' or 'stub' are currently available)
     * @return array array with the variables to fill
     */
    protected function getTemplateVariablesToFill($type)
    {
        switch ($type) {
            case 'factory':
                return [
                    'namespacedModel',
                    'model',
                    'factoryContent'
                ];
                break;
            case 'seeder':
                return [
                    'class',
                    'seederContent'
                ];
            default:
                return [];
                break;
        }
    }
}
