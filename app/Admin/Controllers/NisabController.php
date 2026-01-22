<?php

namespace App\Admin\Controllers;

use App\Enums\NisabUpdateTypeEnum;
use Carbon\Carbon;
use OpenAdmin\Admin\Auth\Permission;
use OpenAdmin\Admin\Controllers\AdminController;
use OpenAdmin\Admin\Facades\Admin;
use OpenAdmin\Admin\Form;
use OpenAdmin\Admin\Grid;
use OpenAdmin\Admin\Layout\Content;
use OpenAdmin\Admin\Show;
use \App\Models\Nisab;
use Illuminate\Support\Facades\DB;
use OpenAdmin\Admin\Widgets\Box; // Make sure this import is present

class NisabController extends AdminController
{
    use ModificationTrait;
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Nisab';

    public function edit($id, Content $content): Content
    {
        Permission::check('nisab.update');
        return parent::edit($id, $content);
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Nisab());
        $this->modifyGrid($grid);

        $startDate = request()->input('startDate');
        $endDate = request()->input('endDate');

        if (!empty($startDate) && !empty($endDate)) {
            $grid->model()->whereBetween('nisab_update_date', [$startDate, $endDate]);
        } else {
            $grid->model()->latest('nisab_update_date')->latest('updated_at')->take(20);
        }

        $grid->model()->orderBy('nisab_update_date', 'desc')->orderBy('updated_at', 'desc');

        $grid->column('gold_value', __('Gold value (per gm)'));
        $grid->column('silver_value', __('Silver value (per gm)'));
        $grid->column('nisab_update_date', __('Nisab update date'));

        $grid->filter(function($filter){
            $filter->disableIdFilter();

            $filter->column(1/2, function ($filter) {
                $filter->like('nisab_update_date', 'Nisab Update Date')->date();
            });
            $filter->column(1/2, function ($filter) {
                $filter->like('gold_value', 'Gold value');
                $filter->like('silver_value', 'Silver value');
            });

            $filter->scope('trashed', 'Deleted Records')->onlyTrashed();
        });

        $grid->header(function ($query) {
            $startDate = request()->input('startDate');
            $endDate = request()->input('endDate');

            if (empty($startDate) || empty($endDate)) {
                $nisabData = $query->select('nisab_update_date', 'gold_value', 'silver_value')
                    ->orderBy('nisab_update_date', 'asc')
                    ->orderBy('updated_at', 'asc')
                    ->take(20)
                    ->get();
            } else {
                $nisabData = $query->select('nisab_update_date', 'gold_value', 'silver_value')
                    ->whereBetween('nisab_update_date', [$startDate, $endDate])
                    ->orderBy('nisab_update_date', 'asc')
                    ->orderBy('updated_at', 'asc')
                    ->get();
            }

            $labels = $nisabData->pluck('nisab_update_date')->reverse()->values()->toArray();
            $goldValues = $nisabData->pluck('gold_value')->reverse()->values()->toArray();
            $silverValues = $nisabData->pluck('silver_value')->reverse()->values()->toArray();

            $lineChart = view('admin.nisab_chart.chart', compact('labels', 'goldValues', 'silverValues'));

            return new Box('Nisab Values Chart', $lineChart);
        });

        $grid->actions(function ($actions) {
            if ($actions->row->trashed()) {
                $actions->disableEdit();
                $actions->disableShow();
                $actions->disableDelete();
            }
            else {
                if (!Admin::user()->inRoles(['administrator', 'admin', 'board_secretary', 'resource_mobilizer'])) {
                    $actions->disableDelete();
                }
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
        $show = new Show(Nisab::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('gold_value', __('Gold value (per gm)'));
        $show->field('silver_value', __('Silver value (per gm)'));
        $show->field('nisab_update_date', __('Nisab update date'));

        $show->field('created_at')->as(function ($time) {
            return Carbon::parse($time)->format('Y-m-d h:i:s A');
        });
        $show->field('updated_at', __('Last Updated at'))->as(function ($time) {
            return Carbon::parse($time)->format('Y-m-d h:i:s A');
        });

        $show->panel()
            ->tools(function ($tools) {
                if (!Admin::user()->inRoles(['administrator', 'admin', 'board_secretary', 'resource_mobilizer'])) {
                    $tools->disableDelete();
                }
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
        $form = new Form(new Nisab());
        $this->modifyForm($form);

        $form->decimal('gold_value', __('Gold value (per gm)'))->rules('required|numeric|min:0|max:99999999');
        $form->decimal('silver_value', __('Silver value (per gm)'))->rules('required|numeric|min:0|max:99999999');
        $form->date('nisab_update_date', __('Nisab update date'))
            ->default(date('Y-m-d'))
            ->rules(['required', 'date', 'before_or_equal:' . date('Y-m-d')]);

        $form->tools(function (Form\Tools $tools) {
            if (!Admin::user()->inRoles(['administrator', 'admin', 'board_secretary', 'resource_mobilizer'])) {
                $tools->disableDelete();
            }
        });

        return $form;
    }
}
