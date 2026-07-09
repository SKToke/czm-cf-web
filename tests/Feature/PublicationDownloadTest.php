<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Publication;
use App\Enums\PublicationTypeEnum;

class PublicationDownloadTest extends TestCase
{
    public function test_guest_download_view_renders_correctly(): void
    {
        $created = false;
        // Find or create a publication for testing
        $publication = Publication::first();
        if (!$publication) {
            $publication = Publication::create([
                'title' => 'Test Publication',
                'active' => true,
                'publication_type' => PublicationTypeEnum::AUDIT_REPORT,
                'published_date' => now()->toDateString(),
            ]);
            $created = true;
        }

        try {
            // Access the download page as a guest (GET request)
            $response = $this->get(route('download', ['id' => $publication->id]));

            // Assert response status is 200 (Success)
            $response->assertStatus(200);

            // Assert that the HTML contains the form pointing to the download route for this publication
            $response->assertSee(route('download', ['id' => $publication->id]));
            $response->assertSee('Login');
            $response->assertSee('Give below information to download the file');
        } finally {
            if ($created) {
                $publication->forceDelete();
            }
        }
    }
}
