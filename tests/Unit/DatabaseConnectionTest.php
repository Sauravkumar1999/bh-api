<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Support\Facades\DB;

class DatabaseConnectionTest extends TestCase
{
    private $database_connection = '';

    function setUp(): void 
    {
        parent::setUp();
        $this->database_connection = env('TEST_DATABASE_CONNECTION','mysql_test');
    }

    public function test_database_connection()
    {
        $this->connectDB()->getPdo();
    }
   
    function connectDB(){
        return DB::connection($this->database_connection);
    }
}
