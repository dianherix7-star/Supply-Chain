<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Port;
use App\Models\Country;

class PortSeeder extends Seeder
{
    public function run(): void
    {
        // Data 60+ major world ports dengan koordinat
        $ports = [
            // ASIA
            ['country_code' => 'CN', 'port_name' => 'Port of Shanghai',    'latitude' => 31.2304, 'longitude' => 121.4737],
            ['country_code' => 'CN', 'port_name' => 'Port of Shenzhen',    'latitude' => 22.5431, 'longitude' => 114.0579],
            ['country_code' => 'CN', 'port_name' => 'Port of Ningbo',      'latitude' => 29.8683, 'longitude' => 121.5440],
            ['country_code' => 'CN', 'port_name' => 'Port of Guangzhou',   'latitude' => 23.1291, 'longitude' => 113.2644],
            ['country_code' => 'CN', 'port_name' => 'Port of Qingdao',     'latitude' => 36.0671, 'longitude' => 120.3826],
            ['country_code' => 'CN', 'port_name' => 'Port of Tianjin',     'latitude' => 39.0042, 'longitude' => 117.7148],
            ['country_code' => 'SG', 'port_name' => 'Port of Singapore',   'latitude' => 1.2644,  'longitude' => 103.8221],
            ['country_code' => 'KR', 'port_name' => 'Port of Busan',       'latitude' => 35.1796, 'longitude' => 129.0756],
            ['country_code' => 'JP', 'port_name' => 'Port of Tokyo',       'latitude' => 35.6762, 'longitude' => 139.6503],
            ['country_code' => 'JP', 'port_name' => 'Port of Yokohama',    'latitude' => 35.4437, 'longitude' => 139.6380],
            ['country_code' => 'JP', 'port_name' => 'Port of Osaka',       'latitude' => 34.6937, 'longitude' => 135.5023],
            ['country_code' => 'HK', 'port_name' => 'Port of Hong Kong',   'latitude' => 22.3193, 'longitude' => 114.1694],
            ['country_code' => 'TW', 'port_name' => 'Port of Kaohsiung',   'latitude' => 22.6273, 'longitude' => 120.3014],
            ['country_code' => 'MY', 'port_name' => 'Port of Klang',       'latitude' => 3.0319,  'longitude' => 101.3895],
            ['country_code' => 'ID', 'port_name' => 'Port of Tanjung Priok','latitude' => -6.1057, 'longitude' => 106.8783],
            ['country_code' => 'ID', 'port_name' => 'Port of Surabaya',    'latitude' => -7.2492, 'longitude' => 112.7508],
            ['country_code' => 'TH', 'port_name' => 'Port of Laem Chabang','latitude' => 13.0782, 'longitude' => 100.8801],
            ['country_code' => 'VN', 'port_name' => 'Port of Ho Chi Minh', 'latitude' => 10.7231, 'longitude' => 106.7230],
            ['country_code' => 'IN', 'port_name' => 'Port of Mumbai',      'latitude' => 18.9388, 'longitude' => 72.8354],
            ['country_code' => 'IN', 'port_name' => 'Port of Chennai',     'latitude' => 13.0827, 'longitude' => 80.2707],
            ['country_code' => 'PK', 'port_name' => 'Port of Karachi',     'latitude' => 24.8607, 'longitude' => 67.0011],
            ['country_code' => 'AE', 'port_name' => 'Port of Jebel Ali',   'latitude' => 24.9857, 'longitude' => 55.0272],
            ['country_code' => 'SA', 'port_name' => 'Port of Jeddah',      'latitude' => 21.3891, 'longitude' => 39.1925],

            // EUROPE
            ['country_code' => 'NL', 'port_name' => 'Port of Rotterdam',   'latitude' => 51.9225, 'longitude' => 4.4791],
            ['country_code' => 'DE', 'port_name' => 'Port of Hamburg',     'latitude' => 53.5753, 'longitude' => 10.0153],
            ['country_code' => 'BE', 'port_name' => 'Port of Antwerp',     'latitude' => 51.2194, 'longitude' => 4.4025],
            ['country_code' => 'GB', 'port_name' => 'Port of Felixstowe',  'latitude' => 51.9574, 'longitude' => 1.3518],
            ['country_code' => 'ES', 'port_name' => 'Port of Algeciras',   'latitude' => 36.1259, 'longitude' => -5.4550],
            ['country_code' => 'ES', 'port_name' => 'Port of Valencia',    'latitude' => 39.4699, 'longitude' => -0.3763],
            ['country_code' => 'IT', 'port_name' => 'Port of Gioia Tauro', 'latitude' => 38.4219, 'longitude' => 15.8992],
            ['country_code' => 'FR', 'port_name' => 'Port of Le Havre',    'latitude' => 49.4942, 'longitude' => 0.1079],
            ['country_code' => 'GR', 'port_name' => 'Port of Piraeus',     'latitude' => 37.9467, 'longitude' => 23.6465],
            ['country_code' => 'PL', 'port_name' => 'Port of Gdansk',      'latitude' => 54.3520, 'longitude' => 18.6466],
            ['country_code' => 'RU', 'port_name' => 'Port of Novorossiysk','latitude' => 44.7232, 'longitude' => 37.7688],

            // AMERICAS
            ['country_code' => 'US', 'port_name' => 'Port of Los Angeles', 'latitude' => 33.7395, 'longitude' => -118.2596],
            ['country_code' => 'US', 'port_name' => 'Port of Long Beach',  'latitude' => 33.7547, 'longitude' => -118.2154],
            ['country_code' => 'US', 'port_name' => 'Port of New York',    'latitude' => 40.6700, 'longitude' => -74.0100],
            ['country_code' => 'US', 'port_name' => 'Port of Houston',     'latitude' => 29.7604, 'longitude' => -95.3698],
            ['country_code' => 'US', 'port_name' => 'Port of Savannah',    'latitude' => 32.0835, 'longitude' => -81.0998],
            ['country_code' => 'CA', 'port_name' => 'Port of Vancouver',   'latitude' => 49.2827, 'longitude' => -123.1207],
            ['country_code' => 'MX', 'port_name' => 'Port of Manzanillo',  'latitude' => 19.0500, 'longitude' => -104.3200],
            ['country_code' => 'PA', 'port_name' => 'Port of Balboa',      'latitude' => 8.9507,  'longitude' => -79.5657],
            ['country_code' => 'BR', 'port_name' => 'Port of Santos',      'latitude' => -23.9608,'longitude' => -46.3280],
            ['country_code' => 'CL', 'port_name' => 'Port of San Antonio', 'latitude' => -33.5928,'longitude' => -71.6200],
            ['country_code' => 'CO', 'port_name' => 'Port of Cartagena',   'latitude' => 10.3910, 'longitude' => -75.4794],

            // AFRICA & MIDDLE EAST
            ['country_code' => 'ZA', 'port_name' => 'Port of Durban',      'latitude' => -29.8587,'longitude' => 31.0218],
            ['country_code' => 'EG', 'port_name' => 'Port Said',           'latitude' => 31.2653, 'longitude' => 32.3019],
            ['country_code' => 'MA', 'port_name' => 'Port of Tanger Med',  'latitude' => 35.8841, 'longitude' => -5.5041],
            ['country_code' => 'KE', 'port_name' => 'Port of Mombasa',     'latitude' => -4.0435, 'longitude' => 39.6682],
            ['country_code' => 'OM', 'port_name' => 'Port of Salalah',     'latitude' => 17.0150, 'longitude' => 54.1152],

            // OCEANIA
            ['country_code' => 'AU', 'port_name' => 'Port of Melbourne',   'latitude' => -37.8136,'longitude' => 144.9631],
            ['country_code' => 'AU', 'port_name' => 'Port of Sydney',      'latitude' => -33.8688,'longitude' => 151.2093],
            ['country_code' => 'NZ', 'port_name' => 'Port of Auckland',    'latitude' => -36.8485,'longitude' => 174.7633],
        ];

        foreach ($ports as $portData) {
            // Cari country berdasarkan country_code
            $country = Country::where('country_code', $portData['country_code'])->first();

            if ($country) {
                Port::firstOrCreate(
                    [
                        'country_id' => $country->id,
                        'port_name'  => $portData['port_name'],
                    ],
                    [
                        'latitude'  => $portData['latitude'],
                        'longitude' => $portData['longitude'],
                    ]
                );
            }
        }

        $this->command->info('✅ ' . count($ports) . ' major world ports seeded.');
    }
}
