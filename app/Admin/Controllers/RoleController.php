<?php

namespace App\Admin\Controllers;

use OpenAdmin\Admin\Controllers\AdminController;
use OpenAdmin\Admin\Form;
use OpenAdmin\Admin\Grid;
use OpenAdmin\Admin\Show;
use \OpenAdmin\Admin\Auth\Database\Role;
use \OpenAdmin\Admin\Auth\Database\Permission;

class RoleController extends AdminController
{
    use ModificationTrait;
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Role';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Role());
        $this->modifyGrid($grid);

        $grid->column('id', 'ID')->sortable();
        $grid->column('slug', trans('admin.slug'));
        $grid->column('name', trans('admin.name'));

        $grid->column('permissions', trans('admin.permission'))->pluck('name')->label()->style('max-width:400px;white-space:normal;word-break:break-all;');

        $grid->actions(function (Grid\Displayers\Actions\Actions $actions) {
            $actions->disableEdit();
            $actions->disableDelete();
        });

        $grid->disableFilter();
        $grid->disableCreateButton();

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
        $show = new Show(Role::findOrFail($id));

        $show->field('id', 'ID');
        $show->field('slug', trans('admin.slug'));
        $show->field('name', trans('admin.name'));
        $show->field('permissions', trans('admin.permissions'))->as(function ($permission) {
            return $permission->pluck('name');
        })->label();
        $show->field('created_at', trans('admin.created_at'));
        $show->field('updated_at', trans('admin.updated_at'));

        $show->panel()
            ->tools(function ($tools) {
                $tools->disableEdit();
                $tools->disableDelete();
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
        $form = new Form(new Role());
        $this->modifyForm($form);

        $form->display('id', 'ID');

        $form->text('slug', trans('admin.slug'))->rules('required');
        $form->text('name', trans('admin.name'))->rules('required');
        $form->listbox('permissions', trans('admin.permissions'))->options(Permission::all()->pluck('name', 'id'))->height(300);

        $form->display('created_at', trans('admin.created_at'));
        $form->display('updated_at', trans('admin.updated_at'));

        $form->tools(function (Form\Tools $tools) {
            $tools->disableDelete();
        });

        return $form;
    }
}
