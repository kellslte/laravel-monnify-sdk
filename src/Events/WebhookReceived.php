<?php

namespace Scwar\Monnify\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Scwar\Monnify\Dto\WebhookPayload;

class WebhookReceived
{
    use Dispatchable, SerializesModels;

    /**
     * Create a new event instance.
     *
     * @param  \Scwar\Monnify\Dto\WebhookPayload  $payload
     * @return void
     */
    public function __construct(
        public WebhookPayload $payload
    ) {
    }
}
