<?php

namespace Scwar\Monnify\Dto;

class WebhookPayload
{
    /**
     * Create a new webhook payload instance.
     *
     * @param  array  $data
     * @return void
     */
    public function __construct(
        public readonly ?string $eventType = null,
        public readonly ?string $eventData = null,
        public readonly ?string $product = null,
        public readonly ?array $raw = null
    ) {
    }

    /**
     * Create a webhook payload from array data.
     *
     * @param  array  $data
     * @return self
     */
    public static function fromArray(array $data): self
    {
        return new self(
            eventType: $data['eventType'] ?? null,
            eventData: $data['eventData'] ?? null,
            product: $data['product'] ?? null,
            raw: $data
        );
    }

    /**
     * Get the decoded event data.
     *
     * @return array|null
     */
    public function getEventData(): ?array
    {
        if (! $this->eventData) {
            return null;
        }

        $decoded = json_decode($this->eventData, true);

        return is_array($decoded) ? $decoded : null;
    }

    /**
     * Convert the payload to an array.
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            'eventType' => $this->eventType,
            'eventData' => $this->eventData,
            'product' => $this->product,
        ];
    }
}
