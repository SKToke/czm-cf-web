<?php

namespace App\Admin\Controllers;

use App\Models\TaggedCategory;
use App\Models\VideoGallery;
use OpenAdmin\Admin\Controllers\AdminController;
use OpenAdmin\Admin\Facades\Admin;
use OpenAdmin\Admin\Form;
use OpenAdmin\Admin\Grid;
use OpenAdmin\Admin\Show;
use \App\Models\Program;
use \App\Models\Category;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class VideoGalleryController extends AdminController
{
    use ModificationTrait;
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Video Gallery';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new VideoGallery());
        $grid->model()->orderBy('updated_at', 'desc');
        $this->modifyGrid($grid);

        $grid->column('title');
        $grid->column('youtube_link');
        $grid->column('image')->image('', 100, 'auto');

        $grid->filter(function($filter){
            $filter->disableIdFilter();

            $filter->column(1/2, function ($filter) {
                $filter->like('title', 'Title');
            });
            $filter->column(1/2, function ($filter) {
                $filter->like('youtube_link', 'Youtube Link');
            });
            $filter->scope('trashed', 'Deleted Videos')->onlyTrashed();
        });

        $grid->actions(function ($actions) {
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
        $show = new Show(VideoGallery::findOrFail($id));

        $show->field('title');
        $show->field('youtube_link');
        $show->field('categories')->as(function ($categories) {
            return $categories->pluck('title')->implode(', ');
        });
        $show->field('image', __('Thumbnail image'))->image('', 100, 'auto');;
        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new VideoGallery());
        $this->modifyForm($form);
        $youtubeRegex = 'regex:/^(https?:\/\/)?(www\.)?(youtube\.com\/watch\?v=|youtu\.be\/)[^\s]+$/';


        $form->text('title', __('Title'))
            ->required()
            ->help('Maximum 70 characters')
            ->rules('min:3|max:70');


        $form->text('youtube_link', __('YouTube Link'))
            ->required()
            ->rules(['required', 'url', $youtubeRegex], ['regex' => 'The YouTube link is invalid.(Reel link is not excepted)']);


        $form->image('image', __('Thumbnail image'))->removable()->rules('mimes:jpg,jpeg,png,webp')
            ->rules('max:10240')->help('Supported file: jpg,jpeg,png,webp & Max file size: 10MB.');

        if ($form->isCreating()) {
            $form->multipleSelect('categories')->options(Category::all()->pluck('title', 'id'))->placeholder('Select categories');
        }

        if ($form->isEditing()) {
            $videoId = request()->route('video_gallery');
            $video = VideoGallery::findOrFail($videoId);

            $selectedCategories = $video->categories ? $video->categories->pluck('id')->toArray() : [];
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
            $video = $form->model();

            if ($form->isCreating()) {

                $formCategories = request()->get('categories');
                $this->manageCategories($formCategories, $video->id);
            } elseif ($form->isEditing()) {
                $video->taggedCategories()->delete();
                $formCategories = request()->get('select_categories');
                if ($formCategories==null){
                    $formCategories=[];
                }
                $this->manageCategories($formCategories, $video->id);
            }
        });

        return $form;
    }

    protected function manageCategories(array $formCategories, int $videoId)
    {
        if ($formCategories != null) {
            foreach ($formCategories as $categoryId) {
                if ($categoryId != null) {
                    $taggedCategory = new TaggedCategory([
                        'category_id' => $categoryId,
                        'parentable_type' => VideoGallery::class,
                        'parentable_id' => $videoId,
                    ]);

                    $taggedCategory->save();
                }
            }
        }
    }
}
