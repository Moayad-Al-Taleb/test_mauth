<?php

namespace Database\Seeders;

use App\Models\Post;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $posts = [
            [
                'title' => 'Introduction to Web Development',
                'body' => 'This post covers the basics of web development...',
                'status' => 1,
            ],
            [
                'title' => 'HTML and CSS Basics',
                'body' => 'This post introduces HTML and CSS, the building blocks of web development...',
                'status' => 1,
            ],
            [
                'title' => 'Getting Started with JavaScript',
                'body' => 'JavaScript is a powerful programming language for web development...',
                'status' => 1,
            ],
            [
                'title' => 'Introduction to PHP',
                'body' => 'PHP is a popular server-side scripting language...',
                'status' => 1,
            ],
            [
                'title' => 'Understanding Databases',
                'body' => 'Databases are essential for storing data in web applications...',
                'status' => 1,
            ],
            [
                'title' => 'Getting Started with Laravel',
                'body' => 'Laravel is a powerful PHP framework for building web applications...',
                'status' => 1,
            ],
            [
                'title' => 'Advanced JavaScript Concepts',
                'body' => 'This post covers advanced topics in JavaScript...',
                'status' => 1,
            ],
            [
                'title' => 'Building RESTful APIs',
                'body' => 'RESTful APIs allow web applications to communicate with each other...',
                'status' => 1,
            ],
            [
                'title' => 'Introduction to Frontend Frameworks',
                'body' => 'Frontend frameworks like React, Vue, and Angular help build dynamic user interfaces...',
                'status' => 1,
            ],
            [
                'title' => 'Deploying Web Applications',
                'body' => 'Deploying web applications involves moving your code to a production server...',
                'status' => 1,
            ],
        ];

        foreach ($posts as $post) {
            Post::create($post);
        }

    }
}
