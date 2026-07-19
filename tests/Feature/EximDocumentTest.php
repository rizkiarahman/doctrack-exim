<?php

namespace Tests\Feature;

use App\Models\EximDocument;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Carbon\Carbon;

class EximDocumentTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that dashboard returns a successful response for authenticated users.
     */
    public function test_dashboard_renders_successfully(): void
    {
        $user = User::factory()->create();

        // Seed some data
        EximDocument::create([
            'no_aju' => 'AJU-999-001',
            'pic' => 'John Doe',
            'tgl_diserahkan' => now()->subDays(2),
            'status' => 'Menunggu Tanda Tangan'
        ]);

        $response = $this->actingAs($user)->get('/');

        $response->assertStatus(200);
        $response->assertViewHas('total_aktif', 1);
        $response->assertViewHas('menunggu_tanda_tangan', 1);
        $response->assertViewHas('sudah_kembali', 0);
        $response->assertViewHas('lewat_deadline', 0);
    }

    /**
     * Test that status automatically transitions to 'Perlu Follow Up' when retrieved after 7 days.
     */
    public function test_document_automatically_becomes_perlu_follow_up_after_7_days(): void
    {
        $user = User::factory()->create();

        // Insert document directly into database to bypass saving events
        \Illuminate\Support\Facades\DB::table('exim_documents')->insert([
            'no_aju' => 'AJU-999-002',
            'pic' => 'Jane Doe',
            'tgl_diserahkan' => now()->subDays(8)->format('Y-m-d'),
            'status' => 'Menunggu Tanda Tangan',
            'created_at' => now(),
            'updated_at' => now()
        ]);

        $id = \Illuminate\Support\Facades\DB::table('exim_documents')->where('no_aju', 'AJU-999-002')->value('id');

        // Verify database entry starts as 'Menunggu Tanda Tangan'
        $this->assertEquals('Menunggu Tanda Tangan', DB_connection_direct_check($id));

        // Retrieve document from DB which triggers 'retrieved' boot logic
        $retrievedDoc = EximDocument::find($id);

        // Verify status has updated to 'Perlu Follow Up'
        $this->assertEquals('Perlu Follow Up', $retrievedDoc->status);

        // Check dashboard registers it as overdue
        $response = $this->actingAs($user)->get('/');
        $response->assertViewHas('lewat_deadline', 1);
    }

    /**
     * Test CRUD: Admin can create document.
     */
    public function test_admin_can_create_document_via_form(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->post(route('documents.store'), [
            'no_aju' => 'AJU-NEW-001',
            'pic' => 'Alice',
            'tgl_diserahkan' => now()->format('Y-m-d'),
            'catatan' => 'Test notes'
        ]);

        $response->assertRedirect(route('documents.index'));
        $this->assertDatabaseHas('exim_documents', [
            'no_aju' => 'AJU-NEW-001',
            'pic' => 'Alice',
            'status' => 'Menunggu Tanda Tangan'
        ]);
    }

    /**
     * Test CRUD: Admin can update document.
     */
    public function test_admin_can_update_document_and_mark_returned(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $doc = EximDocument::create([
            'no_aju' => 'AJU-UPDATE-001',
            'pic' => 'Bob',
            'tgl_diserahkan' => now()->subDays(3),
            'status' => 'Menunggu Tanda Tangan'
        ]);

        $response = $this->actingAs($admin)->put(route('documents.update', $doc->id), [
            'no_aju' => 'AJU-UPDATE-001',
            'pic' => 'Bob',
            'tgl_diserahkan' => $doc->tgl_diserahkan->format('Y-m-d'),
            'tgl_kembali' => now()->format('Y-m-d'),
            'catatan' => 'Returned today'
        ]);

        $response->assertRedirect(route('documents.index'));
        $this->assertDatabaseHas('exim_documents', [
            'id' => $doc->id,
            'status' => 'Sudah Kembali'
        ]);
    }

    /**
     * Test Authorization: Normal User CANNOT create or update documents.
     */
    public function test_user_cannot_create_or_update_documents(): void
    {
        $user = User::factory()->create(['role' => 'user']);
        
        // Try creating
        $responseCreate = $this->actingAs($user)->post(route('documents.store'), [
            'no_aju' => 'AJU-FORBID-001',
            'pic' => 'Charlie',
            'tgl_diserahkan' => now()->format('Y-m-d'),
        ]);

        $responseCreate->assertStatus(403);
        $this->assertDatabaseMissing('exim_documents', [
            'no_aju' => 'AJU-FORBID-001'
        ]);

        // Try updating
        $doc = EximDocument::create([
            'no_aju' => 'AJU-FORBID-002',
            'pic' => 'Charlie',
            'tgl_diserahkan' => now()->subDays(2),
            'status' => 'Menunggu Tanda Tangan'
        ]);

        $responseUpdate = $this->actingAs($user)->put(route('documents.update', $doc->id), [
            'no_aju' => 'AJU-FORBID-002',
            'pic' => 'Charlie',
            'tgl_diserahkan' => $doc->tgl_diserahkan->format('Y-m-d'),
            'tgl_kembali' => now()->format('Y-m-d')
        ]);

        $responseUpdate->assertStatus(403);
        $this->assertDatabaseHas('exim_documents', [
            'id' => $doc->id,
            'status' => 'Menunggu Tanda Tangan' // Status must remain unchanged
        ]);
    }
}

/**
 * Helper function to query DB directly bypassing Eloquent events for testing.
 */
function DB_connection_direct_check($id) {
    return \Illuminate\Support\Facades\DB::table('exim_documents')->where('id', $id)->value('status');
}
