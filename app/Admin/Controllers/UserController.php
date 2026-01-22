<?php

namespace App\Admin\Controllers;

use App\Enums\CountryTypeEnum;
use App\Enums\DistrictTypeEnum;
use App\Enums\GenderTypeEnum;
use App\Enums\ProfessionTypeEnum;
use App\Enums\UserTypeEnum;
use Carbon\Carbon;
use OpenAdmin\Admin\Auth\Database\Role;
use OpenAdmin\Admin\Controllers\AdminController;
use OpenAdmin\Admin\Facades\Admin;
use OpenAdmin\Admin\Form;
use OpenAdmin\Admin\Grid;
use OpenAdmin\Admin\Show;
use \App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserController extends AdminController
{
    use ModificationTrait;
    /**
     * Title for current resource.User
     *
     * @var string
     */
    protected $title = 'User';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new User());
        $this->modifyGrid($grid);

        $grid->model()->orderBy('id', 'desc');

        $grid->column('email', __('Email'));
        $grid->column('first_name', __('First name'));
        $grid->column('last_name', __('Last name'));
        $grid->column('role')->display(function () {
            $roles = $this->roles()->pluck('name')->toArray();
            return implode(', ', $roles);
        });
        $grid->column('country', __('Country'))->display(function ($status) {
            return $status!=0 ? CountryTypeEnum::from($status)->getTitle() : null;
        });
        $grid->column('gender', __('Gender'))->display(function ($status) {
            return $status!=0 ? GenderTypeEnum::from($status)->getTitle() : null;
        });
        $grid->column('user_type', __('User type'))->display(function ($status) {
            return $status!=0 ? UserTypeEnum::from($status)->getTitle() : null;
        });
        $grid->column('active', __('Active'))->bool();
        $grid->column('removed', __('Self Deleted'))->bool();
        $grid->column('email_verified_at', __('Verified User'))->display(function () {
            return $this->email_verified_at == null ? '<i class="icon-times text-danger"></i>' : '<i class="icon-check text-success"></i>' ;
        });

        $grid->disableCreateButton();

        $grid->filter(function($filter){
            $filter->disableIdFilter();

            $filter->column(1/2, function ($filter) {
                $filter->like('email', 'Email');
                $filter->equal('user_type')->radio(UserTypeEnum::toArray());
            });
            $filter->column(1/2, function ($filter) {
                $filter->where(function ($query) {
                    $value = $this->input;
                    $query->whereHas('roles', function ($query) use ($value) {
                        $query->where('name', $value);
                    });

                }, 'Role', 'Role')->select(Role::where('name', '!=', 'administrator')->pluck('name','name')->toArray());

                $filter->where(function ($query) {
                    $query->where('active', True);
                }, 'Is Active')->checkbox([
                    1    => 'Yes'

                ]);
                $filter->scope('trashed', __('Deleted Users'))->where('removed', true);
            });
        });

        $grid->actions(function ($actions) {
            if (!Admin::user()->inRoles(['administrator', 'admin'])) {
                $actions->disableEdit();
            }
            if ($actions->row->admin == true) {
                $actions->disableEdit();
            }
            $actions->disableDelete();
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
        $show = new Show(User::findOrFail($id));

        $show->field('first_name', __('First name'));
        $show->field('last_name', __('Last name'));
        $show->field('Role')->as( function() {
            $roles = $this->roles()->pluck('name')->toArray();
            return implode(', ', $roles);
        });
        $show->field('email', __('Email'));
        $show->field('email_verified_at', __('Email verified at'));
        $show->field('gender', __('Gender'))->as(function ($type) {
            return $type ? $type->getTitle() : null;
        });
        $show->field('date_of_birth', __('Date of birth'));
        $show->field('mobile_no', __('Mobile no'))->as(function ($mbl) {
            return str_replace('_', '', $mbl);
        });
        $show->field('whatsapp_no', __('Whatsapp no'))->as(function ($whatsApp) {
            return str_replace('_', '', $whatsApp);
        });
        $show->field('profession', __('Profession'))->as(function ($profession) {
            return $profession ? $profession->getTitle() : null;
        });
        $show->field('address_line_1', __('Address line 1'));
        $show->field('address_line_2', __('Address line 2'));
        $show->field('post_code', __('Post code'));
        $show->field('thana', __('Thana'));
        $show->field('district', __('District'))->as(function ($district) {
            return $district ? $district->getTitle() : null;
        });
        $show->field('country', __('Country'))->as(function ($country) {
            return $country ? $country->getTitle() : null;
        });
        $show->field('user_type', __('User type'))->as(function ($type) {
            return $type ? $type->getTitle() : null;
        });
        $show->field('contact_person_name', __('Contact person name'));
        $show->field('contact_person_mobile', __('Contact person mobile'));
        $show->field('contact_person_designation', __('Contact person designation'));
        $show->field('active', __('Active'))->using([0 => 'False', 1 => 'True']);
        $show->field('removed', __('Self Deleted'))->using([0 => 'False', 1 => 'True']);
        $show->field('admin', __('Admin'))->using([0 => 'False', 1 => 'True']);
        $show->field('created_at')->as(function ($time) {
            return Carbon::parse($time)->format('Y-m-d h:i:s A');
        });
        $show->field('updated_at', __('Last Updated at'))->as(function ($time) {
            return Carbon::parse($this->updated_at)->format('Y-m-d h:i:s A');
        });

        $user = User::findOrFail($id);
        if($user && $user->admin == true) {
            $show->panel()
                ->tools(function ($tools) {
                    $tools->disableEdit();
                });
        }

        $show->panel()
            ->tools(function ($tools) {
                if (!Admin::user()->inRoles(['administrator', 'admin'])) {
                    $tools->disableEdit();
                }
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
        $form = new Form(new User());
        $this->modifyForm($form);

        $form->email('email', __('Email'))->readonly();
        $form->datetime('email_verified_at', __('Email verified at'))->readonly();
        $form->password('password', trans('admin.password'))->rules('required|confirmed')->default(function ($form) {
            return $form->model()->password;
        });
        $form->password('password_confirmation', trans('admin.password_confirmation'))->rules('required')
            ->default(function ($form) {
                return $form->model()->password;
            });
        $form->ignore(['password_confirmation']);
        $form->text('first_name', __('First name'))->rules('required');
        $form->text('last_name', __('Last name'));
        $form->date('date_of_birth', __('Date of birth'))->default(date('Y-m-d'))->rules(['date', 'before_or_equal:' . date('Y-m-d')]);
        $form->phonenumber('mobile_no', __('Mobile no'))->options(['mask' => '999999999999999'])->help('Maximum 15 digits');
        $form->phonenumber('whatsapp_no', __('Whatsapp no'))->options(['mask' => '999999999999999'])->help('Maximum 15 digits');
        $form->select('gender', __('Gender'))->options(GenderTypeEnum::toArray());
        $form->select('profession', __('Profession'))->options(ProfessionTypeEnum::toArray());
        $form->select('country', __('Country'))->options(CountryTypeEnum::toArray())->when(14, function (Form $form) {
            $form->select('district', __('District'))->options(DistrictTypeEnum::toArray());
            $form->text('thana', __('Thana'));
            $form->text('post_code', __('Post code'));
        });
        $form->textarea('address_line_1', __('Address line 1'));
        $form->textarea('address_line_2', __('Address line 2'));
        $form->select('user_type', __('User type'))->options(UserTypeEnum::toArray())->when(2, function (Form $form) {
            $form->text('contact_person_name', __('Contact person name'));
            $form->text('contact_person_mobile', __('Contact person mobile'));
            $form->text('contact_person_designation', __('Contact person designation'));
        });

        $form->saving(function (Form $form) {
            if ($form->password && $form->model()->password != $form->password) {
                $form->password = Hash::make($form->password);
            }
        });

        $superAdmin = false;
        if ($form->isEditing()) {
            $user = User::findOrFail(request()->route('user'));
            if ($user->admin == true) {
                $superAdmin = true;
                $form->tools(function (Form\Tools $tools) {
                    $tools->disableDelete();
                });
            }
        }
        if (!$superAdmin) {
            $form->multipleSelect('roles', trans('admin.roles'))->options(Role::where('name', '!=', 'administrator')->pluck('name', 'id'));
            $form->switch('active', __('Active'))->default(1);
            $removedValue = User::findOrFail(request()->route('user'))->removed;
            if ($removedValue == 1) {
                $form->switch('removed', __('Self Deleted'))
                    ->value(function () {
                        $user = User::findOrFail(request()->route('user'));
                        if ($user) {
                            return $user->removed;
                        }
                    });
            }
        }

        $form->saved(function (Form $form) {
            if ($form->isEditing()) {
                $user = User::findOrFail(request()->route('user'));
                if ($user && $form->mobile_no) {
                    $user->mobile_no = str_replace('_', '', $form->mobile_no);
                }
                if ($user && $form->whatsapp_no) {
                    $user->whatsapp_no = str_replace('_', '', $form->whatsapp_no);
                }
                if ($user && $form->country != CountryTypeEnum::Bangladesh->value) {
                    $user->district = null;
                    $user->thana = null;
                    $user->post_code = null;
                }
                if ($user && $form->user_type != UserTypeEnum::Business->value) {
                    $user->contact_person_name = null;
                    $user->contact_person_mobile = null;
                    $user->contact_person_designation = null;
                }
                $user->save();
            }
        });

        $form->tools(function (Form\Tools $tools) {
            $tools->disableDelete();
        });

        return $form;
    }
}
