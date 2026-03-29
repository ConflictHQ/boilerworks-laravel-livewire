<?php

use App\Models\User;
use App\Models\WorkflowDefinition;
use App\Models\WorkflowInstance;

beforeEach(function () {
    $this->admin = User::factory()->create();
    $this->admin->assignRole('admin');

    $this->editor = User::factory()->create();
    $this->editor->assignRole('editor');

    $this->viewer = User::factory()->create();
    $this->viewer->assignRole('viewer');

    $this->validStates = [
        [
            'name' => 'draft',
            'label' => 'Draft',
            'is_initial' => true,
            'is_final' => false,
            'color' => 'gray',
        ],
        [
            'name' => 'review',
            'label' => 'Review',
            'is_initial' => false,
            'is_final' => false,
            'color' => 'yellow',
        ],
        [
            'name' => 'approved',
            'label' => 'Approved',
            'is_initial' => false,
            'is_final' => true,
            'color' => 'green',
        ],
    ];

    $this->validTransitions = [
        [
            'from' => 'draft',
            'to' => 'review',
            'label' => 'Submit',
            'conditions' => [],
            'actions' => [],
        ],
        [
            'from' => 'review',
            'to' => 'approved',
            'label' => 'Approve',
            'conditions' => [],
            'actions' => [],
        ],
    ];
});

describe('index', function () {
    it('displays workflow definitions for authorized users', function () {
        WorkflowDefinition::factory()->count(2)->create();

        $this->actingAs($this->viewer)
            ->get(route('workflows.index'))
            ->assertOk()
            ->assertSee('Workflows');
    });
});

describe('show', function () {
    it('displays a single workflow definition', function () {
        $workflow = WorkflowDefinition::factory()->create();

        $this->actingAs($this->viewer)
            ->get(route('workflows.show', $workflow))
            ->assertOk()
            ->assertSee($workflow->name);
    });
});

describe('create', function () {
    it('shows the create form for admins', function () {
        $this->actingAs($this->admin)
            ->get(route('workflows.create'))
            ->assertOk()
            ->assertSee('Create Workflow');
    });

    it('denies the create form for viewers', function () {
        $this->actingAs($this->viewer)
            ->get(route('workflows.create'))
            ->assertForbidden();
    });

    it('denies the create form for editors', function () {
        $this->actingAs($this->editor)
            ->get(route('workflows.create'))
            ->assertForbidden();
    });
});

describe('store', function () {
    it('creates a workflow definition as admin', function () {
        $this->actingAs($this->admin)
            ->post(route('workflows.store'), [
                'name' => 'Approval Flow',
                'description' => 'Standard approval workflow',
                'status' => 'draft',
                'states' => $this->validStates,
                'transitions' => $this->validTransitions,
            ])
            ->assertRedirect(route('workflows.index'));

        $this->assertDatabaseHas('workflow_definitions', [
            'name' => 'Approval Flow',
        ]);
    });

    it('denies workflow creation for viewers', function () {
        $this->actingAs($this->viewer)
            ->post(route('workflows.store'), [
                'name' => 'Forbidden Workflow',
                'description' => 'Should not exist',
                'status' => 'draft',
                'states' => $this->validStates,
                'transitions' => $this->validTransitions,
            ])
            ->assertForbidden();

        $this->assertDatabaseMissing('workflow_definitions', ['name' => 'Forbidden Workflow']);
    });

    it('validates required fields', function () {
        $this->actingAs($this->admin)
            ->post(route('workflows.store'), [])
            ->assertSessionHasErrors(['name', 'status', 'states']);
    });

    it('validates state structure', function () {
        $this->actingAs($this->admin)
            ->post(route('workflows.store'), [
                'name' => 'Bad States',
                'status' => 'draft',
                'states' => [
                    ['name' => 'missing-fields'],
                ],
                'transitions' => [],
            ])
            ->assertSessionHasErrors('states.0.label');
    });
});

describe('update', function () {
    it('updates a workflow definition as admin', function () {
        $workflow = WorkflowDefinition::factory()->create(['name' => 'Old Name']);

        $this->actingAs($this->admin)
            ->put(route('workflows.update', $workflow), [
                'name' => 'New Name',
                'description' => 'Updated',
                'status' => 'published',
                'states' => $this->validStates,
                'transitions' => $this->validTransitions,
            ])
            ->assertRedirect(route('workflows.show', $workflow));

        $this->assertDatabaseHas('workflow_definitions', [
            'uuid' => $workflow->uuid,
            'name' => 'New Name',
            'status' => 'published',
        ]);
    });

    it('denies workflow update for viewers', function () {
        $workflow = WorkflowDefinition::factory()->create(['name' => 'Unchanged']);

        $this->actingAs($this->viewer)
            ->put(route('workflows.update', $workflow), [
                'name' => 'Changed',
                'status' => 'draft',
                'states' => $this->validStates,
                'transitions' => $this->validTransitions,
            ])
            ->assertForbidden();

        $this->assertDatabaseHas('workflow_definitions', [
            'uuid' => $workflow->uuid,
            'name' => 'Unchanged',
        ]);
    });
});

describe('destroy', function () {
    it('soft deletes a workflow definition as admin', function () {
        $workflow = WorkflowDefinition::factory()->create();

        $this->actingAs($this->admin)
            ->delete(route('workflows.destroy', $workflow))
            ->assertRedirect(route('workflows.index'));

        $this->assertSoftDeleted('workflow_definitions', ['uuid' => $workflow->uuid]);
    });

    it('denies workflow deletion for editors', function () {
        $workflow = WorkflowDefinition::factory()->create();

        $this->actingAs($this->editor)
            ->delete(route('workflows.destroy', $workflow))
            ->assertForbidden();

        $this->assertDatabaseHas('workflow_definitions', ['uuid' => $workflow->uuid, 'deleted_at' => null]);
    });
});

describe('instances', function () {
    it('lists instances for a workflow', function () {
        $workflow = WorkflowDefinition::factory()->create();

        $this->actingAs($this->viewer)
            ->get(route('workflows.instances.index', $workflow))
            ->assertOk()
            ->assertSee('Instances');
    });

    it('creates a workflow instance as admin', function () {
        $workflow = WorkflowDefinition::factory()->create();

        $this->actingAs($this->admin)
            ->post(route('workflows.instances.store', $workflow))
            ->assertRedirect(route('workflows.instances.index', $workflow));

        $this->assertDatabaseHas('workflow_instances', [
            'workflow_definition_id' => $workflow->id,
            'current_state' => 'draft',
        ]);
    });

    it('denies instance creation for viewers', function () {
        $workflow = WorkflowDefinition::factory()->create();

        $this->actingAs($this->viewer)
            ->post(route('workflows.instances.store', $workflow))
            ->assertForbidden();
    });
});

describe('transitions', function () {
    it('transitions an instance to a valid next state', function () {
        $workflow = WorkflowDefinition::factory()->create([
            'states' => $this->validStates,
            'transitions' => $this->validTransitions,
        ]);
        $instance = WorkflowInstance::factory()->create([
            'workflow_definition_id' => $workflow->id,
            'current_state' => 'draft',
        ]);

        $this->actingAs($this->admin)
            ->post(route('workflows.instances.transition', [$workflow, $instance]), [
                'to_state' => 'review',
            ])
            ->assertRedirect(route('workflows.instances.index', $workflow));

        $this->assertDatabaseHas('workflow_instances', [
            'uuid' => $instance->uuid,
            'current_state' => 'review',
        ]);

        $this->assertDatabaseHas('transition_logs', [
            'workflow_instance_id' => $instance->id,
            'from_state' => 'draft',
            'to_state' => 'review',
            'performed_by' => $this->admin->id,
        ]);
    });

    it('rejects an invalid transition', function () {
        $workflow = WorkflowDefinition::factory()->create([
            'states' => $this->validStates,
            'transitions' => $this->validTransitions,
        ]);
        $instance = WorkflowInstance::factory()->create([
            'workflow_definition_id' => $workflow->id,
            'current_state' => 'draft',
        ]);

        $this->actingAs($this->admin)
            ->post(route('workflows.instances.transition', [$workflow, $instance]), [
                'to_state' => 'approved',
            ])
            ->assertSessionHasErrors('to_state');

        $this->assertDatabaseHas('workflow_instances', [
            'uuid' => $instance->uuid,
            'current_state' => 'draft',
        ]);
    });

    it('denies transitions for viewers', function () {
        $workflow = WorkflowDefinition::factory()->create([
            'states' => $this->validStates,
            'transitions' => $this->validTransitions,
        ]);
        $instance = WorkflowInstance::factory()->create([
            'workflow_definition_id' => $workflow->id,
            'current_state' => 'draft',
        ]);

        $this->actingAs($this->viewer)
            ->post(route('workflows.instances.transition', [$workflow, $instance]), [
                'to_state' => 'review',
            ])
            ->assertForbidden();

        $this->assertDatabaseHas('workflow_instances', [
            'uuid' => $instance->uuid,
            'current_state' => 'draft',
        ]);
    });

    it('allows transitions for editors', function () {
        $workflow = WorkflowDefinition::factory()->create([
            'states' => $this->validStates,
            'transitions' => $this->validTransitions,
        ]);
        $instance = WorkflowInstance::factory()->create([
            'workflow_definition_id' => $workflow->id,
            'current_state' => 'draft',
        ]);

        $this->actingAs($this->editor)
            ->post(route('workflows.instances.transition', [$workflow, $instance]), [
                'to_state' => 'review',
            ])
            ->assertRedirect(route('workflows.instances.index', $workflow));

        $this->assertDatabaseHas('workflow_instances', [
            'uuid' => $instance->uuid,
            'current_state' => 'review',
        ]);
    });

    it('supports multi-step transitions', function () {
        $workflow = WorkflowDefinition::factory()->create([
            'states' => $this->validStates,
            'transitions' => $this->validTransitions,
        ]);
        $instance = WorkflowInstance::factory()->create([
            'workflow_definition_id' => $workflow->id,
            'current_state' => 'draft',
        ]);

        $this->actingAs($this->admin)
            ->post(route('workflows.instances.transition', [$workflow, $instance]), [
                'to_state' => 'review',
            ])
            ->assertRedirect();

        $this->actingAs($this->admin)
            ->post(route('workflows.instances.transition', [$workflow, $instance]), [
                'to_state' => 'approved',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('workflow_instances', [
            'uuid' => $instance->uuid,
            'current_state' => 'approved',
        ]);

        expect($instance->fresh()->transitionLogs)->toHaveCount(2);
    });
});
