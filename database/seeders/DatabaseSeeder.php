<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(RolesAndPermissionsTableSeeder::class);
        $this->call(UsersTableSeeder::class);
        $this->call(AuthorsTableSeeder::class);
        $this->call(BooksTableSeeder::class);
        $this->call(ReviewsTableSeeder::class);
        $this->call(ReadingHistoriesTableSeeder::class);
        $this->call(AuthorBookTableSeeder::class);
    }
}
