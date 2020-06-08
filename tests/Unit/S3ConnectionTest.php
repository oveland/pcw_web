<?php
namespace Tests\Unit;

use Tests\TestCase;

class S3ConnectionTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testAppsDirectory()
    {
        $allDirectories =  \Storage::disk('s3')->allDirectories();
        $this->assertTrue( collect($allDirectories)->contains('Apps') );
    }
}

