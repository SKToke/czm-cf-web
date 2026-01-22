<?php

namespace App\Admin\Controllers;

use OpenAdmin\Admin\Controllers\AdminController;
use OpenAdmin\Admin\Facades\Admin;
use OpenAdmin\Admin\Form;
use OpenAdmin\Admin\Grid;
use OpenAdmin\Admin\Show;
use \App\Models\Publication;
use App\Enums\PublicationTypeEnum ;
use Carbon\Carbon;



class PublicationController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */

    use ModificationTrait;
    protected $title = 'Publication';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Publication());
        $this->modifyGrid($grid);

        $grid->column('title', __('Title'));
        $grid->column('publication_type')->display(function ($status) {
            return PublicationTypeEnum::from($status)->getTitle();
        });
        $grid->column('published_date', __('Published date'));
        $grid->column('thumbnail_image', __('Thumbnail image'))->image(null, 'auto', 100);
        $grid->column('active', __('Active'))->bool();

        $grid->filter(function($filter){
            $filter->disableIdFilter();

            $filter->column(1/2, function ($filter) {
                $filter->like('title', 'Title');
                $filter->equal('active', __('Active'))->radio([
                    1    => 'Yes',
                    0    => 'No',
                ]);
            });
            $filter->column(1/2, function ($filter) {
                $filter->like('published_date', 'Published Date')->date();
                $filter->like('publication_type', 'Publication Type')->select(PublicationTypeEnum::toArray());
            });

            $filter->scope('trashed', 'Deleted Publications')->onlyTrashed();
        });

        $grid->actions(function ($actions) {
            if ($actions->row->publication_type == PublicationTypeEnum::STATIC_PAGE_PUBLICATIONS) {
                $actions->disableDelete();
            }
            if ($actions->row->trashed()) {
                $actions->disableEdit();
                $actions->disableShow();
                $actions->disableDelete();
            }
            else {
                if (!Admin::user()->inRoles(['administrator', 'admin', 'board_secretary', 'digital_marketer'])) {
                    $actions->disableDelete();
                }
            }
        });

        $grid->model()->orderBy('published_date');

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
        $show = new Show(Publication::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('title', __('Title'));

        $show->field('publication_type', __('Publication type'))->as(function ($status) {
            return $status->getTitle();
        });

        $show->field('published_date', __('Published date'));
        $show->field('thumbnail_image', __('Thumbnail image'))->image();
        $show->field('attachment.file', __('File'))->file();
        $show->field('active', __('Active'))->as(function ($isActive) {
            return $isActive ? 'Yes' : 'No';
        });
        $show->field('created_at')->as(function ($time) {
            return Carbon::parse($time)->format('Y-m-d h:i:s A');
        });
        $show->field('updated_at', __('Last Updated at'))->as(function ($time) {
            return Carbon::parse($time)->format('Y-m-d h:i:s A');
        });

        $publication = Publication::findOrFail($id);
        $show->panel()
            ->tools(function ($tools) use ($publication) {
                if($publication && $publication->publication_type == PublicationTypeEnum::STATIC_PAGE_PUBLICATIONS) {
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
        $form = new Form(new Publication());
        $this->modifyForm($form);

        if ($form->isEditing()) {
            $publicationId = request()->route('publication');
            $publication = Publication::findOrFail($publicationId);

            if ($publication && $publication->publication_type == PublicationTypeEnum::STATIC_PAGE_PUBLICATIONS) {
                $form->text('title', __('Title'))
                    ->rules('required|max:120')
                    ->help('Required. Max length: 120 characters.')->readonly();
                $form->select('publication_type', __('Publication Type'))
                    ->options(PublicationTypeEnum::toArray())
                    ->required()
                    ->default(PublicationTypeEnum::STATIC_PAGE_PUBLICATIONS)->readonly();
                $form->date('published_date', __('Published date'))
                    ->default(date('Y-m-d'))
                    ->rules(['required', 'date', 'before_or_equal:' . date('Y-m-d'), 'after_or_equal:2014-01-01']);
                $form->file('attachment.file', __('Upload File'))
                    ->required()
                    ->removable(false)
                    ->rules('max:102400')
                    ->help('Required. Max file size: 100MB.');
                $form->tools(function (Form\Tools $tools) {
                    $tools->disableDelete();
                });
            } else {
                $form->text('title', __('Title'))
                    ->rules('required|max:120')
                    ->help('Required. Max length: 120 characters.');
                $form->switch('active', __('Active'))->default(1);
                $form->select('publication_type', __('Publication Type'))->options(PublicationTypeEnum::toArray())->required();
                $form->date('published_date', __('Published date'))
                    ->default(date('Y-m-d'))
                    ->rules(['required', 'date', 'before_or_equal:' . date('Y-m-d'), 'after_or_equal:2014-01-01']);
                $form->image('thumbnail_image', __('Thumbnail image'))
                    ->removable()
                    ->rules('mimes:jpg,jpeg,png,webp')
                    ->rules('max:10240')
                    ->help('Supported file: jpg,jpeg,png,webp & Max file size: 10MB.');
                $form->file('attachment.file', __('Upload File'))
                    ->required()
                    ->removable(false)
                    ->rules('max:102400')
                    ->help('Required. Max file size: 100MB.');
            }
        } else {
            $options = PublicationTypeEnum::toArray();
            unset($options[5]);

            $form->text('title', __('Title'))
                ->rules('required|max:120')
                ->help('Required. Max length: 120 characters.');
            $form->switch('active', __('Active'))->default(1);
            $form->select('publication_type', __('Publication Type'))->options($options)->required();
            $form->date('published_date', __('Published date'))
                ->default(date('Y-m-d'))
                ->rules(['required', 'date', 'before_or_equal:' . date('Y-m-d'), 'after_or_equal:2014-01-01']);
            $form->image('thumbnail_image', __('Thumbnail image'))
                ->removable()
                ->rules('mimes:jpg,jpeg,png,webp')
                ->rules('max:10240')
                ->help('Supported file: jpg,jpeg,png,webp & Max file size: 10MB.');
            $form->file('attachment.file', __('Upload File'))
                ->required()
                ->removable(false)
                ->rules('max:102400')
                ->help('Required. Max file size: 100MB.');
        }

        return $form;
    }
}
