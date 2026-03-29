<?php

use App\Enums\FormStatus;
use App\Models\FormDefinition;
use App\Models\User;

beforeEach(function () {
    $this->admin = User::factory()->create();
    $this->admin->assignRole('admin');

    $this->editor = User::factory()->create();
    $this->editor->assignRole('editor');

    $this->viewer = User::factory()->create();
    $this->viewer->assignRole('viewer');

    $this->validSchema = [
        'fields' => [
            [
                'name' => 'full_name',
                'label' => 'Full Name',
                'type' => 'text',
            ],
            [
                'name' => 'email',
                'label' => 'Email',
                'type' => 'email',
            ],
        ],
    ];
});

describe('index', function () {
    it('displays form definitions for authorized users', function () {
        FormDefinition::factory()->count(3)->create();

        $this->actingAs($this->viewer)
            ->get(route('forms.index'))
            ->assertOk()
            ->assertSee('Forms');
    });
});

describe('show', function () {
    it('displays a single form definition', function () {
        $form = FormDefinition::factory()->create();

        $this->actingAs($this->viewer)
            ->get(route('forms.show', $form))
            ->assertOk()
            ->assertSee($form->name);
    });
});

describe('create', function () {
    it('shows the create form for editors', function () {
        $this->actingAs($this->editor)
            ->get(route('forms.create'))
            ->assertOk()
            ->assertSee('Create Form');
    });

    it('denies the create form for viewers', function () {
        $this->actingAs($this->viewer)
            ->get(route('forms.create'))
            ->assertForbidden();
    });
});

describe('store', function () {
    it('creates a form definition as editor', function () {
        $this->actingAs($this->editor)
            ->post(route('forms.store'), [
                'name' => 'Contact Form',
                'slug' => 'contact-form',
                'description' => 'A contact form',
                'status' => 'draft',
                'schema' => $this->validSchema,
            ])
            ->assertRedirect(route('forms.index'));

        $this->assertDatabaseHas('form_definitions', [
            'name' => 'Contact Form',
            'slug' => 'contact-form',
        ]);
    });

    it('denies form creation for viewers', function () {
        $this->actingAs($this->viewer)
            ->post(route('forms.store'), [
                'name' => 'Forbidden Form',
                'slug' => 'forbidden-form',
                'status' => 'draft',
                'schema' => $this->validSchema,
            ])
            ->assertForbidden();

        $this->assertDatabaseMissing('form_definitions', ['name' => 'Forbidden Form']);
    });

    it('validates required fields', function () {
        $this->actingAs($this->editor)
            ->post(route('forms.store'), [])
            ->assertSessionHasErrors(['name', 'slug', 'status', 'schema']);
    });

    it('validates unique slug', function () {
        FormDefinition::factory()->create(['slug' => 'taken-slug']);

        $this->actingAs($this->editor)
            ->post(route('forms.store'), [
                'name' => 'Another Form',
                'slug' => 'taken-slug',
                'status' => 'draft',
                'schema' => $this->validSchema,
            ])
            ->assertSessionHasErrors('slug');
    });

    it('validates schema field types', function () {
        $this->actingAs($this->editor)
            ->post(route('forms.store'), [
                'name' => 'Bad Schema',
                'slug' => 'bad-schema',
                'status' => 'draft',
                'schema' => [
                    'fields' => [
                        [
                            'name' => 'field',
                            'label' => 'Field',
                            'type' => 'invalid_type',
                        ],
                    ],
                ],
            ])
            ->assertSessionHasErrors('schema.fields.0.type');
    });
});

describe('update', function () {
    it('updates a form definition as editor', function () {
        $form = FormDefinition::factory()->create(['name' => 'Old Name']);

        $this->actingAs($this->editor)
            ->put(route('forms.update', $form), [
                'name' => 'New Name',
                'slug' => $form->slug,
                'description' => 'Updated',
                'status' => 'published',
                'schema' => $this->validSchema,
            ])
            ->assertRedirect(route('forms.show', $form));

        $this->assertDatabaseHas('form_definitions', [
            'uuid' => $form->uuid,
            'name' => 'New Name',
            'status' => 'published',
        ]);
    });

    it('denies form update for viewers', function () {
        $form = FormDefinition::factory()->create(['name' => 'Unchanged']);

        $this->actingAs($this->viewer)
            ->put(route('forms.update', $form), [
                'name' => 'Changed',
                'slug' => $form->slug,
                'status' => 'draft',
                'schema' => $this->validSchema,
            ])
            ->assertForbidden();

        $this->assertDatabaseHas('form_definitions', [
            'uuid' => $form->uuid,
            'name' => 'Unchanged',
        ]);
    });
});

describe('destroy', function () {
    it('soft deletes a form definition as admin', function () {
        $form = FormDefinition::factory()->create();

        $this->actingAs($this->admin)
            ->delete(route('forms.destroy', $form))
            ->assertRedirect(route('forms.index'));

        $this->assertSoftDeleted('form_definitions', ['uuid' => $form->uuid]);
    });

    it('denies form deletion for editors', function () {
        $form = FormDefinition::factory()->create();

        $this->actingAs($this->editor)
            ->delete(route('forms.destroy', $form))
            ->assertForbidden();

        $this->assertDatabaseHas('form_definitions', ['uuid' => $form->uuid, 'deleted_at' => null]);
    });
});

describe('submissions', function () {
    it('lists submissions for a form', function () {
        $form = FormDefinition::factory()->published()->create();

        $this->actingAs($this->viewer)
            ->get(route('forms.submissions.index', $form))
            ->assertOk()
            ->assertSee('Submissions');
    });

    it('shows the submission create form for a published form', function () {
        $form = FormDefinition::factory()->published()->create();

        $this->actingAs($this->viewer)
            ->get(route('forms.submissions.create', $form))
            ->assertOk()
            ->assertSee('Submit');
    });

    it('returns 404 for submission create on a draft form', function () {
        $form = FormDefinition::factory()->create(['status' => FormStatus::Draft]);

        $this->actingAs($this->viewer)
            ->get(route('forms.submissions.create', $form))
            ->assertNotFound();
    });

    it('stores a submission for a published form', function () {
        $form = FormDefinition::factory()->published()->create();

        $this->actingAs($this->viewer)
            ->post(route('forms.submissions.store', $form), [
                'data' => [
                    'full_name' => 'Jane Doe',
                    'email' => 'jane@example.com',
                    'message' => 'Hello world',
                ],
            ])
            ->assertRedirect(route('forms.show', $form));

        $this->assertDatabaseHas('form_submissions', [
            'form_definition_id' => $form->id,
        ]);
    });

    it('rejects submission to a draft form', function () {
        $form = FormDefinition::factory()->create(['status' => FormStatus::Draft]);

        $this->actingAs($this->viewer)
            ->post(route('forms.submissions.store', $form), [
                'data' => [
                    'full_name' => 'Jane Doe',
                    'email' => 'jane@example.com',
                ],
            ])
            ->assertNotFound();
    });

    it('validates required submission fields', function () {
        $form = FormDefinition::factory()->published()->create([
            'schema' => [
                'fields' => [
                    [
                        'name' => 'email',
                        'label' => 'Email',
                        'type' => 'email',
                        'required' => true,
                        'options' => [],
                    ],
                ],
            ],
        ]);

        $this->actingAs($this->viewer)
            ->post(route('forms.submissions.store', $form), [
                'data' => [],
            ])
            ->assertSessionHasErrors('data');
    });
});
