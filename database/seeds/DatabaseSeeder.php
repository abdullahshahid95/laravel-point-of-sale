<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Unit;
use App\PurchaseUnit;
class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        Unit::create(["name" => "1 pao", "value" => 0.25, "type" => "kg"]);
        Unit::create(["name" => "Adha kilo", "value" => 0.5, "type" => "kg"]);
        for($i = 1; $i <= 100; $i++)
        {
            Unit::create(["name" => "{$i} kilo", "value" => $i, "type" => "kg"]);
        }
        for($i = 0.1; $i <= 60; $i = $i + 0.1)
        {
            if(($i != 0.5) && ($i != 0.50) && ($i != 0.25) && ($i != 0.250))
            {
                $a = number_format($i, 1);
                
                if(substr($a, strpos($a, ".") + 1, 1) != 0)
                {
                    Unit::create(["name" => "{$a} kilo", "value" => $a, "type" => "kg"]);
                }
            }
        }

        for($i = 1; $i <= 100; $i++)
        {
            Unit::create(["name" => "{$i} gaddi", "value" => $i, "type" => "gaddi"]);
        }

        for($i = 1; $i <= 100; $i++)
        {
            Unit::create(["name" => "{$i} piece", "value" => $i, "type" => "piece"]);
        }

        for($i = 1; $i <= 1200; $i++)
        {
            if($i % 12 != 0)
            {
                Unit::create(["name" => "{$i}", "value" => $i, "type" => "darjan"]);
            }
            else
            {
                $darjan = $i/12;
                Unit::create(["name" => "{$darjan} darjan", "value" => $i, "type" => "darjan"]);
            }
        }

        PurchaseUnit::create(["name" => "1 pao", "value" => 0.25, "type" => "kg"]);
        PurchaseUnit::create(["name" => "Adha kilo", "value" => 0.5, "type" => "kg"]);
        for($i = 1; $i <= 500; $i++)
        {
            PurchaseUnit::create(["name" => "{$i} kilo", "value" => $i, "type" => "kg"]);
        }
        for($i = 0.1; $i <= 60; $i = $i + 0.1)
        {
            if(($i != 0.5) && ($i != 0.50) && ($i != 0.25) && ($i != 0.250))
            {
                $a = number_format($i, 1);
                
                if(substr($a, strpos($a, ".") + 1, 1) != 0)
                {
                    PurchaseUnit::create(["name" => "{$a} kilo", "value" => $a, "type" => "kg"]);
                }
            }
        }

        for($i = 1; $i <= 100; $i++)
        {
            PurchaseUnit::create(["name" => "{$i} gaddi", "value" => $i, "type" => "gaddi"]);
        }

        for($i = 1; $i <= 100; $i++)
        {
            PurchaseUnit::create(["name" => "{$i} piece", "value" => $i, "type" => "piece"]);
        }

        for($i = 1; $i <= 1200; $i++)
        {
            if($i % 12 != 0)
            {
                PurchaseUnit::create(["name" => "{$i}", "value" => $i, "type" => "darjan"]);
            }
            else
            {
                $darjan = $i/12;
                PurchaseUnit::create(["name" => "{$darjan} darjan", "value" => $i, "type" => "darjan"]);
            }
        }
    }
}
