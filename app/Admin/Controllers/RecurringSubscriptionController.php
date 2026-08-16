<?php

namespace App\Admin\Controllers;

use App\Admin\Actions\CancelSubscription;
use App\Admin\Actions\RefundTransaction;
use App\Admin\Actions\SyncSubscription;
use App\Models\RecurringSubscription;
use Carbon\Carbon;
use OpenAdmin\Admin\Controllers\AdminController;
use OpenAdmin\Admin\Form;
use OpenAdmin\Admin\Grid;
use OpenAdmin\Admin\Show;

class RecurringSubscriptionController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Recurring Subscriptions';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new RecurringSubscription());

        $grid->model()->with('donor');
        $grid->model()->withCount([
            'transactions as paid_count' => function ($query) {
                $query->whereIn('payment_status', ['valid', 'success']);
            },
            'transactions as failed_count' => function ($query) {
                $query->where('payment_status', 'failed');
            }
        ]);

        $grid->column('id', 'ID');
        $grid->column('payment_gateway', 'Gateway')->display(function ($gateway) {
            if ($gateway === 'bkash') {
                return '<span class="badge" style="background-color:#e2136e;color:#fff;padding:4px 8px;">bKash</span>';
            }
            return '<span class="badge" style="background-color:#004a98;color:#fff;padding:4px 8px;">SSLCommerz</span>';
        });

        $grid->column('donor.name', 'Donor');
        $grid->column('donor.email', 'Email');
        $grid->column('donor.phone', 'Mobile');
        $grid->column('donor.email')->style('display:none');
        $grid->column('donor.phone')->style('display:none');

        $grid->export(function ($export) {
            $export->column('status', function ($value) {
                return strip_tags($value);
            });
        });

        $grid->column('amount', 'Amount')->display(function ($amount) {
            return number_format($amount, 2) . ' ' . ($this->currency ?? 'BDT');
        });

        $grid->column('status', 'Status')->display(function ($status) {
            return strtoupper($status);
        })->label([
            'active' => 'success',      // green
            'initiated' => 'info',      // blue
            'paused' => 'warning',      // yellow
            'cancelled' => 'danger',    // red
            'expired' => 'default',     // grey
            'failed' => 'danger',       // red
        ]);

        $grid->column('frequency_type', 'Frequency')->display(function ($frequency) {
            return ucfirst($frequency);
        });

        $grid->column('paid_expected', 'Paid / Expected')->display(function () {
            $paid = $this->paid_count;
            $expected = 0;
            if ($this->started_at) {
                if ($this->frequency_type === 'daily') {
                    $expected = Carbon::parse($this->started_at)->diffInDays(now()) + 1;
                }
                if ($this->frequency_type === 'monthly') {
                    $expected = Carbon::parse($this->started_at)->diffInMonths(now()) + 1;
                }
            }
            return "{$paid} / {$expected}";
        });

        $grid->column('started_at', 'Started');
        $grid->column('next_billing_at', 'Next Billing');

        $grid->column('missed_consecutive', 'Missed')->display(function () {
            $transactions = $this->transactions()->orderBy('paid_at', 'desc')->limit(5)->get();
            $missed = 0;
            foreach ($transactions as $txn) {
                if ($txn->payment_status === 'failed') {
                    $missed++;
                } else {
                    break;
                }
            }
            return $missed;
        });

        $grid->filter(function ($filter) {
            $filter->equal('payment_gateway', 'Gateway')->select([
                'bkash' => 'bKash',
                'sslcommerz' => 'SSLCommerz',
            ]);
            $filter->like('donor.name', 'Donor Name');
            $filter->equal('status')->select([
                'active' => 'Active',
                'initiated' => 'Initiated',
                'paused' => 'Paused',
                'cancelled' => 'Cancelled',
                'expired' => 'Expired',
                'failed' => 'Failed',
            ]);
            $filter->between('started_at', 'Started At')->datetime();
        });

        $grid->actions(function ($actions) {
            $actions->add(new SyncSubscription());
            if ($actions->row->status !== 'cancelled') {
                $actions->add(new CancelSubscription());
            }
        });

        return $grid;
    }

    /**
     * Make a show builder.
     *
     * @param mixed $id
     * @return Show
     */
    protected function detail($id)
    {
        $show = new Show(RecurringSubscription::findOrFail($id));

        $show->field('id', 'Subscription ID');
        $show->field('payment_gateway', 'Gateway')->as(function ($gateway) {
            return strtoupper($gateway ?? 'SSLCOMMERZ');
        });
        $show->field('donor.name', 'Donor Name');
        $show->field('subscription_id', 'Gateway Subscription ID');
        $show->field('last_tran_id', 'Request / Last Tran ID');
        $show->field('amount', 'Amount')->as(function ($amount) {
            return number_format($amount, 2) . ' ' . ($this->currency ?? 'BDT');
        });
        $show->field('frequency_type', 'Frequency')->as(function ($freq) {
            return ucfirst($freq);
        });
        $show->field('status', 'Status')->as(function ($status) {
            return strtoupper($status);
        });
        $show->field('started_at', 'Started At');
        $show->field('next_billing_at', 'Next Billing Date');
        $show->field('paused_at', 'Paused At');
        $show->field('cancelled_at', 'Cancelled At');

        $show->transactions('Deduction Transactions', function ($grid) {
            $grid->column('id', 'ID');
            $grid->column('tran_id', 'Transaction ID / TrxID');
            $grid->column('payment_id', 'Payment ID');
            $grid->column('amount', 'Amount')->display(function ($amount) {
                return number_format($amount, 2) . ' ' . ($this->currency ?? 'BDT');
            });
            $grid->column('payment_status', 'Status')->label([
                'valid' => 'success',
                'success' => 'success',
                'failed' => 'danger',
                'refunded' => 'warning',
            ]);
            $grid->column('paid_at', 'Payment Date');

            $grid->actions(function ($actions) {
                $actions->disableView();
                $actions->disableEdit();
                $actions->disableDelete();
                if (in_array($actions->row->payment_status, ['valid', 'success'])) {
                    $actions->add(new RefundTransaction());
                }
            });
        });

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new RecurringSubscription());

        $form->select('payment_gateway', 'Gateway')->options([
            'bkash' => 'bKash',
            'sslcommerz' => 'SSLCommerz',
        ])->required();
        $form->display('donor.name', 'Donor Name');
        $form->display('refer', 'Refer');
        $form->display('subscription_id', 'Subscription ID');
        $form->decimal('amount', __('Amount'));
        $form->text('currency', __('Currency'))->default('BDT');
        $form->select('frequency_type', 'Frequency')->options([
            'daily' => 'Daily',
            'monthly' => 'Monthly',
        ]);
        $form->number('billing_day', __('Billing day'));
        $form->select('status', 'Status')->options([
            'initiated' => 'Initiated',
            'active' => 'Active',
            'paused' => 'Paused',
            'cancelled' => 'Cancelled',
            'expired' => 'Expired',
            'failed' => 'Failed',
        ])->required();
        $form->datetime('started_at', __('Started at'))->default(date('Y-m-d H:i:s'));
        $form->datetime('next_billing_at', __('Next billing at'))->default(date('Y-m-d H:i:s'));
        $form->datetime('paused_at', __('Paused at'))->default(date('Y-m-d H:i:s'));
        $form->datetime('cancel_requested_at', __('Cancel requested at'))->default(date('Y-m-d H:i:s'));
        $form->datetime('cancelled_at', __('Cancelled at'))->default(date('Y-m-d H:i:s'));
        $form->display('last_tran_id', 'Last tran id');
        $form->datetime('last_payment_at', __('Last payment at'))->default(date('Y-m-d H:i:s'));
        $form->text('last_payment_status', __('Last payment status'));

        return $form;
    }
}

