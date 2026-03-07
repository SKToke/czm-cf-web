<?php

namespace App\Admin\Controllers;

use App\Admin\Actions\CancelSubscription;
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
    protected $title = 'RecurringSubscription';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new RecurringSubscription());

        /*$grid->column('id', __('Id'));
        $grid->column('donor_id', __('Donor id'));
        $grid->column('refer', __('Refer'));
        $grid->column('subscription_id', __('Subscription id'));
        $grid->column('amount', __('Amount'));
        $grid->column('currency', __('Currency'));
        $grid->column('frequency_type', __('Frequency type'));
        $grid->column('billing_day', __('Billing day'));
        $grid->column('status', __('Status'));
        $grid->column('started_at', __('Started at'));
        $grid->column('next_billing_at', __('Next billing at'));
        $grid->column('paused_at', __('Paused at'));
        $grid->column('cancel_requested_at', __('Cancel requested at'));
        $grid->column('cancelled_at', __('Cancelled at'));
        $grid->column('last_tran_id', __('Last tran id'));
        $grid->column('last_payment_at', __('Last payment at'));
        $grid->column('last_payment_status', __('Last payment status'));
        $grid->column('created_at', __('Created at'));
        $grid->column('updated_at', __('Updated at'));*/

        $grid->column('id');
        $grid->column('donor.name', 'Donor');
        $grid->column('amount');
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
        $grid->column('frequency_type', 'Type');
        $grid->column('paid_expected', 'Paid / Expected')->display(function () {
            // Successful payments
            $paid = $this->transactions()->where('payment_status', 'valid')->count();
            // Expected cycles
            $expected = 0;
            if ($this->started_at) {
                $start = Carbon::parse($this->started_at);
                if ($this->frequency_type === 'daily') {
                    $expected = $start->diffInDays(now()) + 1;
                } elseif ($this->frequency_type === 'monthly') {
                    $expected = $start->diffInMonths(now()) + 1;
                }
            }
            return "{$paid} / {$expected}";
        });
        $grid->column('started_at');
        $grid->column('next_billing_at');
        $grid->column('missed_payments', 'Missed (Consecutive)')->display(function () {
            $transactions = $this->transactions()->orderBy('paid_at', 'desc')->take(5)->get();
            $missed = 0;
            foreach ($transactions as $txn) {
                if ($txn->payment_status === 'failed') {
                    $missed++;
                } else {
                    break; // stop once a successful payment appears
                }
            }
            return $missed >= 3
                ? "<span class='label label-danger'>{$missed}</span>"
                : "<span class='label label-default'>{$missed}</span>";
        });

        $grid->filter(function ($filter) {
            $filter->like('donor.name', 'Donor');
            $filter->equal('status');
            $filter->between('started_at');
        });
        $grid->actions(function ($actions) {
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

        /*$show->field('id', __('Id'));
        $show->field('donor_id', __('Donor id'));
        $show->field('refer', __('Refer'));
        $show->field('subscription_id', __('Subscription id'));
        $show->field('amount', __('Amount'));
        $show->field('currency', __('Currency'));
        $show->field('frequency_type', __('Frequency type'));
        $show->field('billing_day', __('Billing day'));
        $show->field('status', __('Status'));
        $show->field('started_at', __('Started at'));
        $show->field('next_billing_at', __('Next billing at'));
        $show->field('paused_at', __('Paused at'));
        $show->field('cancel_requested_at', __('Cancel requested at'));
        $show->field('cancelled_at', __('Cancelled at'));
        $show->field('last_tran_id', __('Last tran id'));
        $show->field('last_payment_at', __('Last payment at'));
        $show->field('last_payment_status', __('Last payment status'));
        $show->field('created_at', __('Created at'));
        $show->field('updated_at', __('Updated at'));*/

        $show->field('donor.name');
        $show->field('amount');
        $show->field('frequency_type');
        $show->field('status');
        $show->field('started_at');
        $show->field('next_billing_at');

        /*        $show->recurringTransactions('Transactions', function ($grid) {
                    $grid->column('tran_id');
                    $grid->column('amount');
                    $grid->column('payment_status');
                    $grid->column('paid_at');
                });*/
        $show->transactions('Transactions', function ($grid) {
            $grid->column('tran_id', 'Transaction ID');
            $grid->column('amount');
            $grid->column('currency');
            $grid->column('payment_status')->label([
                'valid' => 'success',
                'failed' => 'danger',
            ]);
            $grid->column('paid_at', 'Payment Date');
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
