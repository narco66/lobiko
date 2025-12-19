<?php

namespace Tests\Feature\Payments;

use App\Models\Paiement;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;
use Tests\TestCase;

class PaymentWebhookSignatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_webhook_requires_valid_signature(): void
    {
        Config::set('services.payments.webhook_secret', 'secret-key');

        $paiement = Paiement::factory()->create([
            'statut' => 'initie',
        ]);

        $body = ['reference_passerelle' => 'GW-123', 'code_autorisation' => 'AUTH'];
        $payload = json_encode($body);
        $signature = hash_hmac('sha256', $payload, 'secret-key');

        $this->postJson(route('payments.confirm', $paiement), $body, ['X-Signature' => $signature])
            ->assertOk()
            ->assertJsonFragment(['statut' => 'confirme']);
    }

    public function test_webhook_with_bad_signature_is_rejected(): void
    {
        Config::set('services.payments.webhook_secret', 'secret-key');

        $paiement = Paiement::factory()->create([
            'statut' => 'initie',
        ]);

        $body = ['reference_passerelle' => 'GW-123', 'code_autorisation' => 'AUTH'];

        $this->postJson(route('payments.confirm', $paiement), $body, ['X-Signature' => 'bad'])
            ->assertForbidden();
    }
}
