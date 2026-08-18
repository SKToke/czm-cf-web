<?php

namespace App\Admin\Actions;

use App\Services\BkashRecurringService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use OpenAdmin\Admin\Actions\RowAction;

class RefundTransaction extends RowAction
{
    public $name = 'Refund';
    public $icon = 'icon-undo';

    public function handle(Model $model, Request $request)
    {
        $amount = (float)($request->get('amount') ?: $model->amount);
        $reason = $request->get('reason') ?: 'Admin initiated refund';

        if ($model->payment_status === 'refunded') {
            return $this->response()->warning('This transaction has already been refunded.')->refresh();
        }

        $subscription = $model->subscription;
        if (!$subscription) {
            return $this->response()->error('Associated subscription not found.')->refresh();
        }

        if ($subscription->payment_gateway === 'bkash') {
            if (!$model->payment_id) {
                return $this->response()->error('bKash Payment ID is missing for this transaction.')->refresh();
            }

            try {
                $bkashService = app(BkashRecurringService::class);
                $res = $bkashService->refundSubscription((int)$model->payment_id, $amount);

                $model->update([
                    'payment_status' => 'refunded',
                    'refund_trx_id' => $res['reverseTrxId'] ?? $res['trxId'] ?? null,
                    'refunded_amount' => $amount,
                    'refunded_at' => now(),
                    'refund_reason' => $reason,
                ]);

                Log::channel('bkash-recurring')->info('Admin refund executed', [
                    'transaction_id' => $model->id,
                    'payment_id' => $model->payment_id,
                    'amount' => $amount,
                    'reason' => $reason,
                    'response' => $res,
                ]);

                return $this->response()->success('bKash refund request submitted successfully for BDT ' . number_format($amount, 2))->refresh();
            } catch (\Exception $e) {
                return $this->response()->error('bKash Refund Error: ' . $e->getMessage())->refresh();
            }
        }

        // For other gateways
        $model->update([
            'payment_status' => 'refunded',
            'refunded_amount' => $amount,
            'refunded_at' => now(),
            'refund_reason' => $reason,
        ]);

        return $this->response()->success('Transaction marked as refunded.')->refresh();
    }

    public function form()
    {
        $this->text('amount', 'Refund Amount (BDT)')->rules('required|numeric');
        $this->textarea('reason', 'Refund Reason')->rules('required');
    }
}
