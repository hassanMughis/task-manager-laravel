<?php

namespace Tests\Feature;

use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_cannot_view_the_dashboard(): void
    {
        $response = $this->get('/dashboard');

        $response->assertRedirect('/login');
    }

    public function test_a_user_can_view_their_dashboard_with_tasks(): void
    {
        $user = User::factory()->create();
        Task::factory()->for($user)->create(['title' => 'My first task']);

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertStatus(200);
        $response->assertSee('My first task');
    }

    public function test_a_user_can_create_a_task(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/tasks', [
            'title' => 'Write the README',
            'description' => 'Explain install and deploy steps.',
            'status' => 'pending',
            'due_date' => now()->addDays(3)->format('Y-m-d'),
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertDatabaseHas('tasks', [
            'user_id' => $user->id,
            'title' => 'Write the README',
        ]);
    }

    public function test_a_task_requires_a_title(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/tasks', [
            'title' => '',
            'status' => 'pending',
        ]);

        $response->assertSessionHasErrors('title');
    }

    public function test_a_user_can_update_their_own_task(): void
    {
        $user = User::factory()->create();
        $task = Task::factory()->for($user)->create(['title' => 'Old title']);

        $response = $this->actingAs($user)->put("/tasks/{$task->id}", [
            'title' => 'New title',
            'status' => 'pending',
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertDatabaseHas('tasks', ['id' => $task->id, 'title' => 'New title']);
    }

    public function test_a_user_can_mark_a_task_completed(): void
    {
        $user = User::factory()->create();
        $task = Task::factory()->for($user)->create(['status' => 'pending']);

        $this->actingAs($user)->patch("/tasks/{$task->id}/toggle");

        $this->assertDatabaseHas('tasks', ['id' => $task->id, 'status' => 'completed']);
    }

    public function test_a_user_can_delete_their_own_task(): void
    {
        $user = User::factory()->create();
        $task = Task::factory()->for($user)->create();

        $response = $this->actingAs($user)->delete("/tasks/{$task->id}");

        $response->assertRedirect('/dashboard');
        $this->assertDatabaseMissing('tasks', ['id' => $task->id]);
    }

    public function test_a_user_cannot_edit_another_users_task(): void
    {
        $owner = User::factory()->create();
        $intruder = User::factory()->create();
        $task = Task::factory()->for($owner)->create();

        $response = $this->actingAs($intruder)->get("/tasks/{$task->id}/edit");

        $response->assertStatus(403);
    }

    public function test_a_user_cannot_delete_another_users_task(): void
    {
        $owner = User::factory()->create();
        $intruder = User::factory()->create();
        $task = Task::factory()->for($owner)->create();

        $response = $this->actingAs($intruder)->delete("/tasks/{$task->id}");

        $response->assertStatus(403);
        $this->assertDatabaseHas('tasks', ['id' => $task->id]);
    }
}
