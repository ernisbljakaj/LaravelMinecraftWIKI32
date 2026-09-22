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
                'description' => 'Craftland is an English-speaking survival server with Towny, a friendly community and weekly events. Land is protected via claims and the server has been running stable for over 10 years with a large map.',
                'user_id' => $admin->id,
            ],
            [
                'name' => 'Hypixel',
                'ip' => 'mc.hypixel.net',
                'version' => '1.21',
                'mode' => 'Minigames',
                'description' => 'The world\'s largest minigames server with over 10,000 concurrent players. Classics such as SkyBlock, BedWars, SkyWars and Duels, plus its own ranking system and cosmetic items.',
                'user_id' => $demoUser->id,
            ],
            [
                'name' => 'Skyblock Kingdom',
                'ip' => 'skyblockkingdom.net',
                'version' => '1.20.4',
                'mode' => 'Skyblock',
                'description' => 'A skyblock server with your own island in the Slimefun style, automated farms, a marketplace and island co-op. Perfect for everyone who enjoys building efficient farms.',
                'user_id' => $demoUser->id,
            ],
            [
                'name' => 'GoldPvP',
                'ip' => 'pvp.goldserver.eu',
                'version' => '1.21',
                'mode' => 'PvP',
                'description' => 'PvP server with anarchy aspects: no regions, no protection, just survival. Gear drops, kits and monthly ranked fights with real prizes.',
                'user_id' => $admin->id,
            ],
            [
                'name' => 'RedTech',
                'ip' => 'play.redtech.de',
                'version' => '1.20.1',
                'mode' => 'Creative',
                'description' => 'Creative server with a plot world for redstone builds and technical works of art. Weekly building contests with a shoutout in the Discord and up to 10,000 blocks per plot.',
                'user_id' => $demoUser->id,
            ],
            [
                'name' => 'WorldNet',
                'ip' => 'mc.worldnet.org',
                'version' => '1.21.4',
                'mode' => 'Vanilla',
                'description' => 'Pure vanilla gameplay with a friendly, small community. Whitelist process, 1.21.4, hard mode and a completely unmodified world for authenticity fans.',
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

        WikiPage::whereIn('title', [
            'Einfache automatische Farm mit Redstone',
            'Die perfekte Eisenfarm bauen',
            'Moderne Minecraft-Häuser: Grundlagen',
            'Verzaubern wie ein Profi',
            'Villager-Trading: Der beste Handel mit Zombie-Heilung',
        ])->delete();

        $wikiTemplates = [
            [
                'category' => 'Redstone',
                'title' => 'Simple automatic redstone farm',
                'excerpt' => 'Learn how to build a basic AFK farm using an observer, a piston and a few repeaters.',
                'content' => "A redstone comparator detects whenever the state of a block changes. That is the foundation of most automatic farms.\n\nHere is how to build a simple fish farm that feeds you automatically:\n1. Place an observer and aim it at some water.\n2. Put a piston underneath it that moves a sand block.\n3. Connect the observer output with redstone to a locking system.\n\nIt is important that the circuit stays loop-shaped: a clock made from two repeaters makes sure the piston extends at exactly the right moment.",
            ],
            [
                'category' => 'Farming',
                'title' => 'Building the perfect iron farm',
                'excerpt' => 'Iron farms give you endless iron ingots without mining – here is how to build a reliable version.',
                'content' => "Iron golems spawn when enough villagers are nearby and a zombie threatens them. A working iron farm uses exactly this mechanic.\n\nYou will need:\n- At least 3 villagers\n- A zombie cell at the fighting area\n- A hopper with a chest below the kill zone\n\nThe spawn radius has to be dealt with: place the villagers at least 5 blocks above the killing channel and block their line of sight so the golem falls straight into the hopper.",
            ],
            [
                'category' => 'Building',
                'title' => 'Modern Minecraft houses: the basics',
                'excerpt' => 'Shapes, materials and lighting – tips for houses that don\'t look like boxes.',
                'content' => "A modern house starts with a good floor plan. Avoid perfect squares and instead plan an L-shaped or staggered build.\n\nMaterial tips:\n- Combine concrete with wood and glass in cool tones\n- Place light sources like glowstone invisibly under stairs\n- Use slabs and chests to break up columns\n\nRoof shapes: a flat roof with a fascia band or a shed roof. Don't forget fences, balconies and an atrium – good proportions make the difference.",
            ],
            [
                'category' => 'Enchanting',
                'title' => 'Enchant like a pro',
                'excerpt' => 'Reach maximum enchantments: from the optimal library to the best enchantment level.',
                'content' => "Enchanting tables use the bookshelves around them: for level 30 you need 15 shelves, placed in a row around the table.\n\nTips for the maximum yield:\n- Always enchant at level 30 with lapis lazuli\n- Repair your tools first – the secret factor increases\n- Use enchanted books to transfer enchantments\n\nRemember: the luck of everything is decided by RNG – combine enchantments strategically on a village smith anvil.",
            ],
            [
                'category' => 'Mobs',
                'title' => 'Villager trading: the best deal with zombie curing',
                'excerpt' => 'Cure zombie villagers for ridiculous discounts and build a perfect trading hall around them.',
                'content' => "Cured zombie villagers give huge discounts – and re-cured ones keep stacking. Here is how to profit from villager trading.\n\nSteps:\n1. Find a zombie villager in a basement or a desert\n2. Trap it with a cart so the sun doesn't burn it\n3. Throw a splash potion of weakness and feed it a golden apple\n4. Wait for the cure and build a trading hall around it\n\nThe effect stays permanent – perfect for pure survival servers like our vanilla server WorldNet.",
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