<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\SharedFile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SharedFileTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_access_shared_files(): void
    {
        $user = User::factory()->create(['role' => 'user']);

        $response = $this->actingAs($user)->get('/shared-files');

        $response->assertStatus(200);
        $response->assertSee('Berbagi File &amp; Drive Tim', false);
    }

    public function test_user_can_upload_shared_file(): void
    {
        $user = User::factory()->create(['role' => 'user']);

        $file = UploadedFile::fake()->create('dokumen_export.pdf', 100, 'application/pdf');

        $response = $this->actingAs($user)->post('/shared-files', [
            'file' => $file,
        ]);

        $response->assertRedirect('/shared-files');
        $this->assertDatabaseHas('shared_files', [
            'original_name' => 'dokumen_export.pdf',
            'user_id' => $user->id,
        ]);
    }
}
