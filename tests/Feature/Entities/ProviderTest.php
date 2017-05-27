<?php

namespace Tests\Feature\Entities;

use App\Entities\Database;
use App\Entities\Dataset;
use App\Entities\Provider;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ProviderTest extends TestCase
{
    use DatabaseMigrations;

    public function attachDatabase($provider)
    {
        $database = factory(Database::class)->create();
        $provider->databases()->attach($database->id);
    }


    public function test_provider_can_have_many_databases()
    {
        $provider = factory(Provider::class)->create();

        $this->attachDatabase($provider);
        $this->attachDatabase($provider);

        $databases = Provider::whereCode($provider->code)->first()->databases;

        foreach ($databases as $database)
        {
            $this->assertEquals($provider->code, $database->providers->first()->code);
        }
    }
}