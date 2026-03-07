<?php

namespace App\Admin\Actions;

use Illuminate\Database\Eloquent\Model;
use OpenAdmin\Admin\Actions\RowAction;

class CancelSubscription extends RowAction
{
    public $name = 'Cancel';
    public $icon = 'icon-times';

    public function handle(Model $model)
    {
        $model->update([
            'status' => 'cancelled',
            'canceled_at' => now()
        ]);

        return $this->response()->success('Subscription cancelled')->refresh();
    }

}
