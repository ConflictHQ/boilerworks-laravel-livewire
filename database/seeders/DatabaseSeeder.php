<?php

namespace Database\Seeders;

use App\Enums\FormStatus;
use App\Models\Category;
use App\Models\FormDefinition;
use App\Models\FormSubmission;
use App\Models\Item;
use App\Models\User;
use App\Models\WorkflowDefinition;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(PermissionSeeder::class);

        $admin = User::firstOrCreate(
            ['email' => 'admin@boilerworks.dev'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('password'),
            ]
        );
        $admin->assignRole('admin');

        $editor = User::firstOrCreate(
            ['email' => 'editor@boilerworks.dev'],
            [
                'name' => 'Editor User',
                'password' => Hash::make('password'),
            ]
        );
        $editor->assignRole('editor');

        $viewer = User::firstOrCreate(
            ['email' => 'viewer@boilerworks.dev'],
            [
                'name' => 'Viewer User',
                'password' => Hash::make('password'),
            ]
        );
        $viewer->assignRole('viewer');

        $categories = Category::factory()->count(5)->create([
            'created_by' => $admin->id,
        ]);

        $categories->each(function ($category) use ($admin) {
            Item::factory()->count(3)->create([
                'category_id' => $category->id,
                'created_by' => $admin->id,
            ]);
        });

        if (config('features.forms')) {
            $contactForm = FormDefinition::create([
                'name' => 'Contact Form',
                'slug' => 'contact',
                'description' => 'General contact form for inquiries.',
                'status' => FormStatus::Published,
                'schema' => [
                    'fields' => [
                        ['name' => 'full_name', 'label' => 'Full Name', 'type' => 'text', 'required' => true, 'placeholder' => 'Your name', 'options' => []],
                        ['name' => 'email', 'label' => 'Email', 'type' => 'email', 'required' => true, 'placeholder' => 'you@example.com', 'options' => []],
                        ['name' => 'subject', 'label' => 'Subject', 'type' => 'select', 'required' => true, 'placeholder' => '', 'options' => [
                            ['label' => 'General', 'value' => 'general'],
                            ['label' => 'Support', 'value' => 'support'],
                            ['label' => 'Sales', 'value' => 'sales'],
                        ]],
                        ['name' => 'message', 'label' => 'Message', 'type' => 'textarea', 'required' => true, 'placeholder' => 'Your message...', 'options' => []],
                    ],
                ],
                'created_by' => $admin->id,
            ]);

            FormSubmission::factory()->count(5)->create([
                'form_definition_id' => $contactForm->id,
                'created_by' => $editor->id,
            ]);

            FormDefinition::create([
                'name' => 'Feedback Survey',
                'slug' => 'feedback',
                'description' => 'Customer satisfaction survey.',
                'status' => FormStatus::Draft,
                'schema' => [
                    'fields' => [
                        ['name' => 'rating', 'label' => 'Rating', 'type' => 'radio', 'required' => true, 'placeholder' => '', 'options' => [
                            ['label' => 'Excellent', 'value' => '5'],
                            ['label' => 'Good', 'value' => '4'],
                            ['label' => 'Average', 'value' => '3'],
                            ['label' => 'Poor', 'value' => '2'],
                            ['label' => 'Terrible', 'value' => '1'],
                        ]],
                        ['name' => 'comments', 'label' => 'Comments', 'type' => 'textarea', 'required' => false, 'placeholder' => 'Any additional feedback?', 'options' => []],
                    ],
                ],
                'created_by' => $admin->id,
            ]);
        }

        if (config('features.workflows')) {
            WorkflowDefinition::create([
                'name' => 'Approval Workflow',
                'description' => 'Standard two-step approval process.',
                'status' => 'published',
                'states' => [
                    ['name' => 'draft', 'label' => 'Draft', 'is_initial' => true, 'is_final' => false, 'color' => '#6b7280'],
                    ['name' => 'pending_review', 'label' => 'Pending Review', 'is_initial' => false, 'is_final' => false, 'color' => '#f59e0b'],
                    ['name' => 'approved', 'label' => 'Approved', 'is_initial' => false, 'is_final' => true, 'color' => '#10b981'],
                    ['name' => 'rejected', 'label' => 'Rejected', 'is_initial' => false, 'is_final' => true, 'color' => '#ef4444'],
                ],
                'transitions' => [
                    ['from' => 'draft', 'to' => 'pending_review', 'label' => 'Submit for Review', 'conditions' => [], 'actions' => []],
                    ['from' => 'pending_review', 'to' => 'approved', 'label' => 'Approve', 'conditions' => [['type' => 'user_has_role', 'value' => 'admin']], 'actions' => []],
                    ['from' => 'pending_review', 'to' => 'rejected', 'label' => 'Reject', 'conditions' => [['type' => 'user_has_role', 'value' => 'admin']], 'actions' => []],
                    ['from' => 'rejected', 'to' => 'draft', 'label' => 'Revise', 'conditions' => [], 'actions' => []],
                ],
                'created_by' => $admin->id,
            ]);
        }
    }
}
