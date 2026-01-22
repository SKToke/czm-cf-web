<?php

namespace App\Models;

use App\Enums\NotificationFrequencyTypeEnum;
use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Storage;
use OpenAdmin\Admin\Auth\Database\Role;

/**
 * Class Administrator.
 *
 * @property Role[] $roles
 */
class AdminUser extends Model implements AuthenticatableContract
{
    use Authenticatable;
    use HasPermissions;

    protected $fillable = ['email', 'password', 'first_name', 'last_name', 'email_verified_at', 'active', 'admin'];

    /**
     * Create a new Eloquent model instance.
     *
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        $connection = config('admin.database.connection') ?: config('database.default');

        $this->setConnection($connection);

        $this->setTable(config('admin.database.users_table'));

        parent::__construct($attributes);
    }

    /**
     * Get avatar attribute.
     *
     * @param string $avatar
     *
     * @return string
     */
    public function getAvatarAttribute($avatar)
    {
        if (url()->isValidUrl($avatar)) {
            return $avatar;
        }

        $disk = config('admin.upload.disk');

        if ($avatar && array_key_exists($disk, config('filesystems.disks'))) {
            return Storage::disk(config('admin.upload.disk'))->url($avatar);
        }

        $default = config('admin.default_avatar') ?: '/vendor/open-admin/open-admin/gfx/user.svg';

        return admin_asset($default);
    }

    /**
     * A user has and belongs to many roles.
     *
     * @return BelongsToMany
     */
    public function roles(): BelongsToMany
    {
        $pivotTable = config('admin.database.role_users_table');

        $relatedModel = config('admin.database.roles_model');

        return $this->belongsToMany($relatedModel, $pivotTable, 'user_id', 'role_id');
    }

    /**
     * A User has and belongs to many permissions.
     *
     * @return BelongsToMany
     */
    public function permissions(): BelongsToMany
    {
        $pivotTable = config('admin.database.user_permissions_table');

        $relatedModel = config('admin.database.permissions_model');

        return $this->belongsToMany($relatedModel, $pivotTable, 'user_id', 'permission_id');
    }

    public function getUnreadNotifications()
    {
        $user = User::where('email', $this->email)->first();
        if ($user) {
            return $user->getUnreadNotifications();
        }
        return null;
    }

    public function hasValidRoles(): bool
    {
        $user = User::where('email', $this->email)->first();
        if ($user) {
            return $user->hasValidRoles();
        }
        return false;
    }
}
