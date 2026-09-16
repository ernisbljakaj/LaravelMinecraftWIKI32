<?php

namespace Database\Seeders;

use App\Models\Comment;
use App\Models\Favorite;
use App\Models\Server;
use App\Models\Tag;
use App\Models\User;
use App\Models\WikiPage;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::updateOrCreate(
            ['email' => 'admin@example.com'],
            ['name' => 'Admin', 'password' => 'password', 'role' => 'admin']
        );

        $demoUser = User::updateOrCreate(
            ['email' => 'steve@example.com'],
            ['name' => 'Steve', 'password' => 'password', 'role' => 'user']
        );

        $tags = collect([
            ['name' => 'Survival', 'color' => '#22c55e'],
            ['name' => 'PvP', 'color' => '#ef4444'],
            ['name' => 'Creative', 'color' => '#f59e0b'],
            ['name' => 'Skyblock', 'color' => '#38bdf8'],
            ['name' => 'Bedwars', 'color' => '#a855f7'],
            ['name' => 'Minigames', 'color' => '#ec4899'],
            ['name' => 'Anarchy', 'color' => '#ef4444'],
            ['name' => 'Vanilla', 'color' => '#84cc16'],
            ['name' => 'Redstone', 'color' => '#dc2626'],
        ])->map(fn ($tag) => Tag::firstOrCreate(['slug' => Str::slug($tag['name'])], $tag));

        $serversData = [
            [
                'name' => 'Craftland',
                'ip' => 'play.craftland.de',
                'version' => '1.21.4',
                'mode' => 'Survival',
                'description' => 'Craftland ist ein deutschsprachiger Survival-Server mit Towny, freundlicher Community und wöchentlichen Events. Land wird über Claims geschützt und der Server läuft seit über 10 Jahren stabil mit einer großen Map.',
                'user_id' => $admin->id,
            ],
            [
                'name' => 'Hypixel',
                'ip' => 'mc.hypixel.net',
                'version' => '1.21',
                'mode' => 'Minigames',
                'description' => 'Der weltweit größte Minigames-Server mit über 10.000 gleichzeitigen Spielern. Klassiker wie SkyBlock, BedWars, SkyWars und Duels, dazu ein eigenes Rangsystem und kosmetische Gegenstände.',
                'user_id' => $demoUser->id,
            ],
            [
                'name' => 'Skyblock Kingdom',
                'ip' => 'skyblockkingdom.net',
                'version' => '1.20.4',
                'mode' => 'Skyblock',
                'description' => 'Ein Skyblock-Server mit eigener Insel im Slimefun-Style, automatisierten Farmen, Marktplatz und Islands-Co-op. Perfekt für alle, die gerne effiziente Farmen bauen.',
                'user_id' => $demoUser->id,
            ],
            [
                'name' => 'GoldPvP',
                'ip' => 'pvp.goldserver.eu',
                'version' => '1.21',
                'mode' => 'PvP',
                'description' => 'PvP-Server mit Anarchy-Aspekten: Keine Regionen, kein Schutz, nur Überleben. Gearbeute, Kits und monatliche Ranglisten-Kämpfe mit echten Preisen.',
                'user_id' => $admin->id,
            ],
            [
                'name' => 'RedTech',
                'ip' => 'play.redtech.de',
                'version' => '1.20.1',
                'mode' => 'Creative',
                'description' => 'Kreativ-Server mit Plot-Welt für Redstone-Bauten und technische Kunstwerke. Wöchentliche Bauwettbewerbe mit Shoutout im Discord und bis zu 10.000 Blöcke pro Plot.',
                'user_id' => $demoUser->id,
            ],
            [
                'name' => 'WeltNetz',
                'ip' => 'mc.weltnetz.org',
                'version' => '1.21.4',
                'mode' => 'Vanilla',
                'description' => 'Reines Vanilla-Gameplay mit freundlicher, kleiner Community. Whitelist-Verfahren, 1.21.4, Hard-Mode und eine komplett unmodifizierte Welt für Authentisch-Genießer.',
                'user_id' => $admin->id,
            ],
        ];

        foreach ($serversData as $data) {
            $server = Server::updateOrCreate(
                ['name' => $data['name']],
                array_merge($data, ['approved' => true, 'featured' => static::featured($data['name'])])
            );

            match ($data['mode']) {
                'Survival' => $server->tags()->sync($tags->where('name', 'Survival')->first()),
                'Minigames' => $server->tags()->sync([
                    $tags->where('name', 'Minigames')->first()->id,
                    $tags->where('name', 'Bedwars')->first()->id,
                ]),
                'Skyblock' => $server->tags()->sync([
                    $tags->where('name', 'Skyblock')->first()->id,
                    $tags->where('name', 'Survival')->first()->id,
                ]),
                'PvP' => $server->tags()->sync([
                    $tags->where('name', 'PvP')->first()->id,
                    $tags->where('name', 'Anarchy')->first()->id,
                ]),
                'Creative' => $server->tags()->sync([
                    $tags->where('name', 'Creative')->first()->id,
                    $tags->where('name', 'Redstone')->first()->id,
                ]),
                'Vanilla' => $server->tags()->sync([
                    $tags->where('name', 'Vanilla')->first()->id,
                    $tags->where('name', 'Survival')->first()->id,
                ]),
                default => null,
            };
        }

        $wikiTemplates = [
            [
                'category' => 'Redstone',
                'title' => 'Einfache automatische Farm mit Redstone',
                'excerpt' => 'Lerne, wie du mit einem Observer, einem Kolben und ein paar Verstärkern eine simple AFK-Farm baust.',
                'content' => "Ein Redstone-Komparator erkennt, wenn sich der Zustand eines Blocks ändert. Das ist die Grundlage der meisten automatischen Farmen.\n\nSo baust du eine einfache Schildkröten-fressende Fischerei-Farm:\n1. Baue einen Observer und richte ihn auf Wasser.\n2. Platziere einen Kolben darunter, der einen Sandblock bewegt.\n3. Verbinde den Observer-Ausgang mit Redstone zu einem Notfall-Sperrsystem.\n\nWichtig ist, dass der Kreislauf sternförmig bleibt: Ein Taktgeber mit zwei Verstärkern sorgt dafür, dass der Kolben im richtigen Moment ausfährt.",
            ],
            [
                'category' => 'Farmen',
                'title' => 'Die perfekte Eisenfarm bauen',
                'excerpt' => 'Eisenfarmen liefern dir ohne Mining endlose Eisenbarren – so baust du eine zuverlässige Variante.',
                'content' => "Eisen-Golems spawnen, wenn genug Dorfbewohner in der Nähe sind und ein Zombie sie bedroht. Eine funktionierende Eisenfarm nutzt genau diesen Mechanismus.\n\nBenötigt werden:\n- Mindestens 3 Dorfbewohner\n- Eine Zombie-Zelle am Kampfplatz\n- Einen Sammeltrichter mit Truhe unter dem Tötungsbereich\n\nDer Spawn-Radius muss überwunden werden: Platziere die Dorfbewohner mindestens 5 Blöcke über dem Tötungskanal und versperre die Sicht so, dass der Golem direkt in den Trichter fällt.",
            ],
            [
                'category' => 'Bauen',
                'title' => 'Moderne Minecraft-Häuser: Grundlagen',
                'excerpt' => 'Shapes, Materialmix und Licht setzen – Tipps für Häuser, die nicht aussehen wie Kisten.',
                'content' => "Ein modernes Haus beginnt mit einem guten Grundriss. Vermeide perfekte Quadrate und plane stattdessen einen L-förmigen oder versetzten Bau.\n\nMaterial-Tipps:\n- Kombiniere Beton mit Holz und Glas in Kalttönen\n- Setze Lichtquellen wie Hellstein unsichtbar unter Treppen\n- Nutze Abstellgleise und Kästen, um Säulen aufzulockern\n\nDachformen: Flachdach mit Friesband oder Pultdach. Vergiss Einzäunungen, Balkone und ein Atrium nicht – gute Proportionen machen den Unterschied.",
            ],
            [
                'category' => 'Enchanting',
                'title' => 'Verzaubern wie ein Profi',
                'excerpt' => 'Maximale Verzauberungen erreichen: von der optimalen Bibliothek bis zur besten Verzauberungs-Stufe.',
                'content' => "Verzauberungstische nutzen umliegende Bücherregale: Für Stufe 30 brauchst du 15 Regale, die in einer Reihe um den Tisch platziert werden müssen.\n\nTipps für die maximale Ausbeute:\n- Verzaubere immer mit Stufe 30 und Lapislazuli\n- Repariere Werkzeuge vorher – der Geheimnis-Faktor steigt\n- Nutze Bücher mit Kommoden-Fleisch, um Verzauberungen zu übertragen\n\nDenke daran: Das Glück aller Dinge wird per RNG bestimmt – kombiniere Verzauberungen strategisch auf einem Dorfschmied-Amboss.",
            ],
            [
                'category' => 'Mobs',
                'title' => 'Villager-Trading: Der beste Handel mit Zombie-Heilung',
                'excerpt' => 'Heile Zombie-Villager für lächerliche Rabatte und baue so eine perfekte Handelshalle.',
                'content' => "Geheilte Zombie-Villager geben massive Rabatte – und erneut geheilte kumulieren. So profitierst du beim Dorfbewohner-Handel.\n\nSchritte:\n1. Finde einen Zombie-Villager in einem Keller oder einer Wüste\n2. Sperre ihn mit einer Karre, damit die Sonne ihn nicht verbrennt\n3. Wirf ihm einen Schleimbeutel mit Verzauberung zu\n4. Warte auf die Heilung und baue darum eine Handelshalle\n\nDer Effekt bleibt dauerhaft – perfekt für reine Survival-Server wie unseren Vanilla-Server WeltNetz.",
            ],
        ];

        foreach ($wikiTemplates as $page) {
            WikiPage::updateOrCreate(
                ['title' => $page['title']],
                array_merge($page, [
                    'approved' => true,
                    'user_id' => $admin->id,
                ])
            );
        }

        $servers = Server::all();
        $users = User::where('role', 'user')->get();

        Comment::truncate();
        Favorite::truncate();

        foreach ($servers->take(5) as $server) {
            $server->comments()->create([
                'user_id' => $users->random()->id,
                'body' => fake()->sentence(12),
                'approved' => true,
            ]);
        }

        $first = Server::first();
        foreach ($users as $user) {
            $user->favoritedServers()->attach($first->id);
        }

        foreach (WikiPage::all() as $page) {
            if (fake()->boolean(40)) {
                Comment::create([
                    'user_id' => $users->random()->id,
                    'commentable_type' => WikiPage::class,
                    'commentable_id' => $page->id,
                    'body' => fake()->sentence(10),
                    'approved' => true,
                ]);
            }
        }
    }

    protected static function featured(string $name): bool
    {
        return in_array($name, ['Craftland', 'Hypixel', 'RedTech']);
    }
}
