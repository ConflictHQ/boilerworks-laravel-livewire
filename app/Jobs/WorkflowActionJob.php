<?php

namespace App\Jobs;

use App\Models\WorkflowInstance;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class WorkflowActionJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public int $tries = 3;

    public int $backoff = 60;

    public function __construct(
        public WorkflowInstance $instance,
        public array $action,
    ) {}

    public function handle(): void
    {
        $type = $this->action['type'] ?? null;

        match ($type) {
            'send_email' => $this->sendEmail(),
            'update_field' => $this->updateField(),
            'call_webhook' => $this->callWebhook(),
            default => Log::warning('Unknown workflow action type', ['type' => $type, 'instance' => $this->instance->uuid]),
        };
    }

    private function sendEmail(): void
    {
        $to = $this->action['to'] ?? null;
        $subject = $this->action['subject'] ?? 'Workflow Notification';
        $body = $this->action['body'] ?? '';

        if (! $to) {
            Log::warning('send_email action missing "to" address', ['instance' => $this->instance->uuid]);

            return;
        }

        Mail::raw($body, function ($message) use ($to, $subject) {
            $message->to($to)->subject($subject);
        });

        Log::info('Workflow email sent', ['instance' => $this->instance->uuid, 'to' => $to]);
    }

    private function updateField(): void
    {
        $workflowable = $this->instance->workflowable;

        if (! $workflowable) {
            return;
        }

        $field = $this->action['field'] ?? null;
        $value = $this->action['value'] ?? null;

        if (! $field) {
            Log::warning('update_field action missing "field"', ['instance' => $this->instance->uuid]);

            return;
        }

        $workflowable->update([$field => $value]);

        Log::info('Workflow field updated', [
            'instance' => $this->instance->uuid,
            'field' => $field,
            'value' => $value,
        ]);
    }

    private function callWebhook(): void
    {
        $url = $this->action['url'] ?? null;

        if (! $url) {
            Log::warning('call_webhook action missing "url"', ['instance' => $this->instance->uuid]);

            return;
        }

        $response = Http::timeout(30)->post($url, [
            'instance_uuid' => $this->instance->uuid,
            'current_state' => $this->instance->current_state,
            'definition_uuid' => $this->instance->definition->uuid,
            'action' => $this->action,
        ]);

        Log::info('Workflow webhook called', [
            'instance' => $this->instance->uuid,
            'url' => $url,
            'status' => $response->status(),
        ]);
    }
}
