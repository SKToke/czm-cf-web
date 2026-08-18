<?php

namespace App\Admin\Actions;

use App\Services\BkashRecurringService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use OpenAdmin\Admin\Actions\RowAction;

class SyncSubscription extends RowAction
{
    public $name = 'Sync Gateway';
    public $icon = 'icon-sync';

    public function handle(Model $model)
    {
        if ($model->payment_gateway === 'bkash') {
            try {
                $bkashService = app(BkashRecurringService::class);
                $query = null;
                if ($model->subscription_id) {
                    $query = $bkashService->querySubscription((int)$model->subscription_id);
                } elseif ($model->last_tran_id) {
                    $query = $bkashService->querySubscriptionByRequestId($model->last_tran_id);
                }

                if ($query && isset($query['status'])) {
                    $status = strtolower($query['status']);
                    $localStatus = match ($status) {
                        'succeeded', 'verified', 'active' => 'active',
                        'cancelled', 'canceled' => 'cancelled',
                        'expired' => 'expired',
                        'failed' => 'failed',
                        default => $status
                    };

                    $model->update([
                        'status' => $localStatus,
                        'subscription_id' => $query['id'] ?? $model->subscription_id,
                        'subscription_status_onreq' => $query['status'] ?? $model->subscription_status_onreq,
                        'next_billing_at' => isset($query['nextPaymentDate']) ? now()->parse($query['nextPaymentDate']) : $model->next_billing_at,
                    ]);

                    return $this->response()->success('Synced with bKash: Status is ' . $query['status'])->refresh();
                }

                return $this->response()->warning('bKash query returned no status data.')->refresh();
            } catch (\Exception $e) {
                return $this->response()->error('bKash Sync Error: ' . $e->getMessage())->refresh();
            }
        }

        return $this->response()->info('Sync completed for ' . ($model->payment_gateway ?? 'gateway'))->refresh();
    }
}
