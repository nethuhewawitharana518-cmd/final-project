<?php

namespace App\Console\Commands;

use App\Models\DirectoryPlace;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class ImportTrincomaleeDirectory extends Command
{
    /**
     * php artisan directory:import-trincomalee
     */
    protected $signature = 'directory:import-trincomalee';

    protected $description = 'Import hotels, resorts, restaurants, cafes and bakeries in Trincomalee District from OpenStreetMap (100% free, no API key/billing needed)';

    // South, West, North, East — generously covers the whole Trincomalee District
    // with margin, so nothing near the edges gets missed.
    protected string $bbox = '8.15,80.70,9.10,81.35';

    public function handle(): int
    {
        $query = <<<OVERPASS
[out:json][timeout:90];
(
  node["tourism"="hotel"]({$this->bbox});
  node["tourism"="guest_house"]({$this->bbox});
  node["tourism"="resort"]({$this->bbox});
  node["amenity"="restaurant"]({$this->bbox});
  node["amenity"="fast_food"]({$this->bbox});
  node["amenity"="cafe"]({$this->bbox});
  node["shop"="bakery"]({$this->bbox});
  way["tourism"="hotel"]({$this->bbox});
  way["tourism"="resort"]({$this->bbox});
  way["tourism"="guest_house"]({$this->bbox});
  way["amenity"="restaurant"]({$this->bbox});
);
out center;
OVERPASS;

        $this->info('Querying OpenStreetMap Overpass API for Trincomalee District...');
        $this->comment('(This can take 20-60 seconds — the public server is shared and sometimes slow.)');

        try {
            $response = Http::asForm()
                ->withHeaders([
                    'User-Agent' => 'FoodRescueTrincomalee-StudentProject/1.0 (Laravel HTTP client)',
                    'Accept'     => 'application/json, text/plain, */*',
                ])
                ->timeout(120)
                ->post('https://overpass-api.de/api/interpreter', ['data' => $query]);
        } catch (\Exception $e) {
            $this->error('Could not reach Overpass API: ' . $e->getMessage());
            $this->line('Try again in a minute — the free public server occasionally rate-limits.');
            return self::FAILURE;
        }

        if (!$response->successful()) {
            $this->error('Overpass API request failed with status: ' . $response->status());
            $this->line('If this keeps happening, try again later — it is a shared free server.');
            return self::FAILURE;
        }

        $elements = $response->json('elements', []);
        $this->info('Received ' . count($elements) . ' raw map elements. Filtering and saving named places...');

        $saved   = 0;
        $skipped = 0;

        foreach ($elements as $el) {
            $tags = $el['tags'] ?? [];
            $name = $tags['name'] ?? null;

            // Skip unnamed places — useless for an autocomplete-by-name feature
            if (!$name) {
                $skipped++;
                continue;
            }

            // Nodes have lat/lon directly; ways only have a computed center
            // (because a "way" is a shape made of multiple points).
            $lat = $el['lat'] ?? ($el['center']['lat'] ?? null);
            $lon = $el['lon'] ?? ($el['center']['lon'] ?? null);
            if (!$lat || !$lon) {
                $skipped++;
                continue;
            }

            $category = $tags['tourism'] ?? $tags['amenity'] ?? $tags['shop'] ?? 'other';

            $addressParts = array_filter([
                $tags['addr:housenumber'] ?? null,
                $tags['addr:street'] ?? null,
                $tags['addr:suburb'] ?? $tags['addr:city'] ?? null,
            ]);
            $address = count($addressParts) ? implode(', ', $addressParts) : null;

            DirectoryPlace::updateOrCreate(
                ['osm_id' => $el['type'] . '_' . $el['id']],
                [
                    'name'      => $name,
                    'category'  => $category,
                    'address'   => $address,
                    'latitude'  => $lat,
                    'longitude' => $lon,
                ]
            );

            $saved++;
        }

        $this->newLine();
        $this->info("Done — saved/updated {$saved} named places (skipped {$skipped} unnamed/incomplete entries).");
        $this->line('Run this command again any time to refresh the list (e.g. monthly via a scheduled task).');

        return self::SUCCESS;
    }
}
