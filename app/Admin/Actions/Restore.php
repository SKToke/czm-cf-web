<?php

namespace App\Admin\Actions;

use OpenAdmin\Admin\Actions\RowAction;
use Illuminate\Database\Eloquent\Model;

class Restore extends RowAction
{
    public $name = 'Restore';
    public $icon = 'icon-trash-restore';

    public function handle (Model $model)
    {
        $model->restore();

        return $this->response()->success('Restore')->refresh();
    }

    public function dialog()
    {
        $this->confirm('Are you sure you want to restore?');
    }
}
