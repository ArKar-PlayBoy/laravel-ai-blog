<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Comment;
use App\Models\Post;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class BlogSeeder extends Seeder
{
    /**
     * Seed the blog with sample data.
     */
    public function run(): void
    {
        // 2 dedicated users
        $alice = User::factory()->create([
            'name' => 'Alice',
            'email' => 'alice@example.com',
            'password' => Hash::make('password'),
        ]);

        $bob = User::factory()->create([
            'name' => 'Bob',
            'email' => 'bob@example.com',
            'password' => Hash::make('password'),
        ]);

        // Assign roles to users
        $adminRole = Role::where('name', 'admin')->firstOrFail();
        $superAdminRole = Role::where('name', 'super_admin')->firstOrFail();

        $alice->roles()->attach($superAdminRole->id);
        $bob->roles()->attach($adminRole->id);

        $users = [$alice, $bob];

        // 5 pre-defined categories
        $categoryNames = ['Technology', 'Lifestyle', 'Travel', 'Food', 'Science'];
        $categories = collect($categoryNames)->map(
            fn (string $name) => Category::factory()->create(['name' => $name])
        )->all();

        // 20 posts
        $posts = [];
        for ($i = 0; $i < 20; $i++) {
            $posts[] = Post::factory()->create([
                'user_id' => fake()->randomElement($users)->id,
                'category_id' => fake()->randomElement($categories)->id,
                'featured_image' => 'https://picsum.photos/800/600?random='.($i + 1000),
            ]);
        }

        // 40 comments
        for ($i = 0; $i < 40; $i++) {
            Comment::factory()->create([
                'user_id' => fake()->randomElement($users)->id,
                'post_id' => fake()->randomElement($posts)->id,
            ]);
        }
    }
}
