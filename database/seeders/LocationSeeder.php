<?php

namespace Database\Seeders;

use App\Models\Location;
use Illuminate\Database\Seeder;
use Keboola\Csv\CsvReader;

class LocationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (!Location::exists()) {
            $locationFile = new CsvReader(__DIR__ . '/csv/location.csv');
            Location::unsetEventDispatcher();

            foreach ($locationFile as $locationRow) {
                $address = $locationRow[0];
                $postal_code = $locationRow[1];
                $country = $locationRow[2];
                $federal_district = $locationRow[3];
                $region_type = $locationRow[4];
                $region = $locationRow[5];
                $city = $locationRow[6];
                $timezone = $locationRow[7];
                $lat_long = sprintf('%s,%s', $locationRow[8], $locationRow[9]);

                Location::updateOrCreate([
                    'address' => $address,
                ], [
                    'postal_code' => $postal_code,
                    'country' => $country,
                    'federal_district' => $federal_district,
                    'region_type' => $region_type,
                    'region' => $region,
                    'city' => $city,
                    'timezone' => $timezone,
                    'lat_long' => $lat_long,
                ]);
            }
        }
    }
}
