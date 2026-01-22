<?php

namespace App\Admin\Controllers;

use App\Models\PhotoGallery;
use App\Models\TaggedCategory;
use Carbon\Carbon;
use OpenAdmin\Admin\Controllers\AdminController;
use OpenAdmin\Admin\Facades\Admin;
use OpenAdmin\Admin\Form;
use OpenAdmin\Admin\Grid;
use OpenAdmin\Admin\Show;
use \App\Models\Category;


class PhotoGalleryController extends AdminController
{
    use ModificationTrait;
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Photo Gallery';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new PhotoGallery());
        $grid->model()->orderBy('updated_at', 'desc');
        $this->modifyGrid($grid);

        $grid->column('title');
        $grid->column('image')->image('', 100, 'auto');
        $grid->column('updated_at', __('Last Updated at'))->display(function () {
            return Carbon::parse($this->updated_at)->format('Y-m-d h:i:s A');
        });

        $grid->filter(function($filter){
            $filter->disableIdFilter();
            $filter->like('title', 'Title');
            $filter->scope('trashed', 'Deleted photos')->onlyTrashed();
        });

        $grid->actions(function ($actions) {
            if ($actions->row->trashed()) {
                $actions->disableEdit();
                $actions->disableShow();
                $actions->disableDelete();
            }
            else {
                if (!Admin::user()->inRoles(['administrator', 'admin'])) {
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
        $show = new Show(PhotoGallery::findOrFail($id));

        $show->field('title');
        $show->field('categories')->as(function ($categories) {
            return $categories->pluck('title')->implode(', ');
        });
        $show->field('image', __('image'))->image('', 100, 'auto');
        $show->field('created_at', __('Created at'))->as(function ($time) {
            return Carbon::parse($time)->format('Y-m-d h:i:s A');
        });
        $show->field('updated_at', __('Updated at'))->as(function ($time) {
            return Carbon::parse($time)->format('Y-m-d h:i:s A');
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
        $form = new Form(new PhotoGallery());
        $this->modifyForm($form);

        $form->text('title', __('Title'))
            ->required()
            ->help('Maximum 30 characters')
            ->rules('min:3|max:30');

        $form->image('image', __(' image'))->removable()->required()->rules('mimes:jpg,jpeg,png,webp')
            ->rules('max:10240')->help('Supported file: jpg,jpeg,png,webp & Max file size: 10MB.');

        if ($form->isCreating()) {
            $form->multipleSelect('categories')->options(Category::all()->pluck('title', 'id'))->placeholder('Select categories');
        }

        if ($form->isEditing()) {
            $photoId = request()->route('photo_gallery');
            $photo = PhotoGallery::findOrFail($photoId);

            $selectedCategories = $photo->categories ? $photo->categories->pluck('id')->toArray() : [];
            $form->multipleSelect('select_categories')->options(Category::all()->pluck('title', 'id'))->default($selectedCategories);
        }

        $form->saving(function (Form $form) {
            $fieldName = 'select_categories';
            $form->builder()->fields()->each(function ($field, $id) use ($form, $fieldName) {
                if ($field->column() === $fieldName) {
                    $form->builder()->fields()->forget($id);
                }
            });
        });

        $form->saved(function (Form $form) {
            $photo = $form->model();

            if ($form->isCreating()) {

                $formCategories = request()->get('categories');
                $this->manageCategories($formCategories, $photo->id);
            } elseif ($form->isEditing()) {
                $photo->taggedCategories()->delete();
                $formCategories = request()->get('select_categories');
                if ($formCategories==null){
                    $formCategories=[];
                }
                $this->manageCategories($formCategories, $photo->id);
            }
        });

        return $form;
    }

    protected function manageCategories(array $formCategories, int $photoId)
    {
        if ($formCategories != null) {
            foreach ($formCategories as $categoryId) {
                if ($categoryId != null) {
                    $taggedCategory = new TaggedCategory([
                        'category_id' => $categoryId,
                        'parentable_type' => PhotoGallery::class,
                        'parentable_id' => $photoId,
                    ]);

                    $taggedCategory->save();
                }
            }
        }
    }
}
