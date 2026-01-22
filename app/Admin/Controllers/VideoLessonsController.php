<?php

namespace App\Admin\Controllers;

use App\Enums\VideoLessonTypeEnum;
use App\Models\TaggedCategory;
use App\Models\VideoGallery;
use App\Models\VideoLesson;
use OpenAdmin\Admin\Controllers\AdminController;
use OpenAdmin\Admin\Facades\Admin;
use OpenAdmin\Admin\Form;
use OpenAdmin\Admin\Grid;
use OpenAdmin\Admin\Show;
use \App\Models\Program;
use \App\Models\Category;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class VideoLessonsController extends AdminController
{
    use ModificationTrait;
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Video Lessons';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new VideoLesson());
        $grid->model()->orderBy('updated_at', 'desc');
        $this->modifyGrid($grid);

        $grid->column('title');
        $grid->column('youtube_link');
        $grid->column('lesson_type')->display(function ($status) {
            return VideoLessonTypeEnum::from($status)->getTitle();
        });
        $grid->column('image')->image('', 100, 'auto');

        $grid->filter(function($filter){
            $filter->disableIdFilter();

            $filter->column(1/2, function ($filter) {
                $filter->like('title', 'Title');
                $filter->like('youtube_link', 'Youtube Link');
            });
            $filter->column(1/2, function ($filter) {
                $filter->equal('lesson_type', 'Lesson Type')->radio(VideoLessonTypeEnum::toArray());
            });

            $filter->scope('trashed', 'Deleted Video Lessons')->onlyTrashed();
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
        $show = new Show(VideoLesson::findOrFail($id));

        $show->field('title');
        $show->field('youtube_link');
        $show->field('lesson_type', __('lesson type'))->as(function ($type) {
            return VideoLessonTypeEnum::from($type)->getTitle();
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
        $form = new Form(new VideoLesson());
        $this->modifyForm($form);
        $youtubeRegex = 'regex:/^(https?:\/\/)?(www\.)?(youtube\.com\/watch\?v=|youtu\.be\/)[^\s]+$/';

        $form->text('title', __('Title'))
            ->required()
            ->help('Maximum 70 characters')
            ->rules('min:3|max:70');

        $form->text('youtube_link', __('YouTube Link'))
            ->required()
            ->rules(['required', 'url', $youtubeRegex], ['regex' => 'The YouTube link is invalid.(Reel link is not excepted)']);

        $form->select('lesson_type', __('Lesson Type'))
            ->addElementClass('custom-required')
            ->options([
                VideoLessonTypeEnum::Zakat_Is_the_Right_Of_The_Deprived_In_Wealth->value => VideoLessonTypeEnum::Zakat_Is_the_Right_Of_The_Deprived_In_Wealth->name,
                VideoLessonTypeEnum::Fiqh_Of_Zakat->value => VideoLessonTypeEnum::Fiqh_Of_Zakat->name
            ])->default(VideoLessonTypeEnum::Zakat_Is_the_Right_Of_The_Deprived_In_Wealth->value)
            ->rules([new \App\Rules\NonRemovableSelectField()]);
        $form->image('image', __('Thumbnail image'))->removable()->rules('mimes:jpg,jpeg,png,webp')
            ->rules('max:10240')->help('Supported file: jpg,jpeg,png,webp & Max file size: 10MB.');

        return $form;
    }

}
