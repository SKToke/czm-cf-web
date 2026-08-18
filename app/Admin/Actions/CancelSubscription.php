<?php

namespace App\Admin\Actions;

use App\Services\BkashRecurringService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use OpenAdmin\Admin\Actions\RowAction;

class CancelSubscription extends RowAction
{
    public $name = 'Cancel';
    public $icon = 'icon-times';

    public function handle(Model $model)
    {
        // If bKash gateway
        if ($model->payment_gateway === 'bkash' && $model->subscription_id) {
            try {
                $bkashService = app(BkashRecurringService::class);
                $bkashService->cancelSubscription((int)$model->subscription_id, 'Admin Cancelled');
            } catch (\Exception $e) {
                Log::channel('bkash-recurring')->error('Admin cancel bKash error: ' . $e->getMessage());
            }
        }

        // If SSLCommerz gateway
        if ($model->payment_gateway === 'sslcommerz' && $model->subscription_id) {
            try {
                $refer = config('sslcommerz.' . config('sslcommerz.mode') . '.store_refer');
                $storeId = config('sslcommerz.' . config('sslcommerz.mode') . '.store_id');
                $storePass = config('sslcommerz.' . config('sslcommerz.mode') . '.store_password');

                $payload = [
                    'refer' => $refer,
                    'store_id' => $storeId,
                    'store_passwd' => $storePass,
                    'subscription_id' => $model->subscription_id,
                    'action' => 'disableSubscription'
                ];
                $url = config('sslcommerz.apiDomain') . config('sslcommerz.apiUrl.check');
                Http::asForm()->post($url, $payload);
            } catch (\Exception $e) {
                Log::channel('sslcommerz')->error('Admin cancel SSLCommerz error: ' . $e->getMessage());
            }
        }

        $model->update([
            'status' => 'cancelled',
            'cancelled_at' => now(),
            'cancelled_by' => 'ADMIN',
        ]);

        return $this->response()->success('Subscription cancelled on gateway and locally.')->refresh();
    }
}

