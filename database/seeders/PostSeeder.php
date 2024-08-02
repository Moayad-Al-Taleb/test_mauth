<?php

namespace Database\Seeders;

use App\Models\Post;
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
                'title_ar' => 'مقدمة في تطوير الويب',
                'title_en' => 'Introduction to Web Development',
                'body_ar' => 'هذا المنشور يغطي أساسيات تطوير الويب...',
                'body_en' => 'This post covers the basics of web development...',
                'status' => '0',
            ],
            [
                'title_ar' => 'أساسيات HTML و CSS',
                'title_en' => 'HTML and CSS Basics',
                'body_ar' => 'هذا المنشور يقدم HTML و CSS، اللبنات الأساسية لتطوير الويب...',
                'body_en' => 'This post introduces HTML and CSS, the building blocks of web development...',
                'status' => '0',
            ],
            [
                'title_ar' => 'البدء مع جافا سكريبت',
                'title_en' => 'Getting Started with JavaScript',
                'body_ar' => 'جافا سكريبت هي لغة برمجة قوية لتطوير الويب...',
                'body_en' => 'JavaScript is a powerful programming language for web development...',
                'status' => '0',
            ],
            [
                'title_ar' => 'مقدمة في PHP',
                'title_en' => 'Introduction to PHP',
                'body_ar' => 'PHP هي لغة برمجة جانب الخادم شائعة...',
                'body_en' => 'PHP is a popular server-side scripting language...',
                'status' => '0',
            ],
            [
                'title_ar' => 'فهم قواعد البيانات',
                'title_en' => 'Understanding Databases',
                'body_ar' => 'قواعد البيانات ضرورية لتخزين البيانات في تطبيقات الويب...',
                'body_en' => 'Databases are essential for storing data in web applications...',
                'status' => '0',
            ],
            [
                'title_ar' => 'البدء مع لارافيل',
                'title_en' => 'Getting Started with Laravel',
                'body_ar' => 'Laravel هو إطار PHP قوي لبناء تطبيقات الويب...',
                'body_en' => 'Laravel is a powerful PHP framework for building web applications...',
                'status' => '0',
            ],
            [
                'title_ar' => 'مفاهيم جافا سكريبت المتقدمة',
                'title_en' => 'Advanced JavaScript Concepts',
                'body_ar' => 'هذا المنشور يغطي المواضيع المتقدمة في جافا سكريبت...',
                'body_en' => 'This post covers advanced topics in JavaScript...',
                'status' => '0',
            ],
            [
                'title_ar' => 'بناء واجهات برمجة التطبيقات RESTful',
                'title_en' => 'Building RESTful APIs',
                'body_ar' => 'واجهات برمجة التطبيقات RESTful تسمح لتطبيقات الويب بالتواصل مع بعضها البعض...',
                'body_en' => 'RESTful APIs allow web applications to communicate with each other...',
                'status' => '0',
            ],
            [
                'title_ar' => 'مقدمة في الأطر الأمامية',
                'title_en' => 'Introduction to Frontend Frameworks',
                'body_ar' => 'الأطر الأمامية مثل React و Vue و Angular تساعد في بناء واجهات مستخدم ديناميكية...',
                'body_en' => 'Frontend frameworks like React, Vue, and Angular help build dynamic user interfaces...',
                'status' => '0',
            ],
        ];

        foreach ($posts as $post) {
            $newPost = new Post();
            $newPost->setTranslations('title', [
                'ar' => $post['title_ar'],
                'en' => $post['title_en'],
            ]);
            $newPost->setTranslations('body', [
                'ar' => $post['body_ar'],
                'en' => $post['body_en'],
            ]);
            $newPost->status = $post['status'];
            $newPost->save();
        }
    }
}
