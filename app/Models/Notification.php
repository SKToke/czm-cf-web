<?php

namespace App\Models;

use App\Events\RealTimeMessage;
use App\Enums\NotifiableUserTypeEnum;
use Beste\Json;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use App\Models\Attachment;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Mail;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification as PushNotification;

class Notification extends Model
{
    use SoftDeletes;
    use HasFactory;

    protected $fillable = ['notification_title', 'type', 'notification_description', 'send_mail', 'user_type', 'mail_subject', 'mail_body', 'campaign_id'];

    public static $rules = [
        'title' => 'required|max:120',
        'description' => 'max:300',
    ];

    protected $casts = [
        'user_type' => NotifiableUserTypeEnum::class,
    ];

    public static function boot()
    {
        parent::boot();

        static::deleting(function ($notice) {
            $notice->attachments->each(function ($attachment) {
                $attachment->delete();
            });
        });

        static::saved(function ($notification) {
            $userIds = $notification->users()->pluck('users.id')->toArray();
            if (count($userIds) > 0) {
                event(new RealTimeMessage(implode(',', $userIds)));

                //mobile push notification
                foreach ($userIds as $userId) {
                    $user = User::find($userId);
                    if ($user && $user->device_token) {
                        $mobileNotification = [
                            'notification_id' => $notification->id,
                            'notification_title' => $notification->notification_title,
                            'notification_description' => $notification->notification_description,
                            'campaign' => optional($notification->campaign)->title,
                            'created_at' => $notification->created_at->format('Y-m-d H:i:s'),
                        ];

                        $deviceToken = $user->device_token;

                        $title = (string)$notification->notification_title;
                        $description = (string)$notification->notification_description;
                        $plainTextDescription = strip_tags($description);

                        $firebase = (new Factory)
                            ->withServiceAccount(Storage::path('json/firebase-credentials.json'));

                        $messaging = $firebase->createMessaging();

                        $message = CloudMessage::withTarget('token', $deviceToken)
                            ->withNotification(PushNotification::create($title, $plainTextDescription))
                            ->withData($mobileNotification);

                        $messaging->send($message);
                    }
                }

                if ($notification->send_mail == true) {
                    foreach ($userIds as $userId) {
                        $user = User::find($userId);
                        if ($user) {
                            Mail::send('email.notificationEmail', ['mailBody' => $notification->mail_body], function($message) use($user, $notification) {
                                $message->to($user->email);
                                $message->subject($notification->mail_subject);

                                $allAttachments = $notification->attachments;
                                $attachments = [];
                                foreach ($allAttachments as $attachment) {
                                    $filePath = '/admin/' . $attachment->file;
                                    $fileExists = Storage::disk('public')->exists($filePath);
                                    if ($fileExists) {
                                        $attachments[] = public_path('storage'. $filePath);
                                    }
                                }

                                foreach ($attachments as $attachment) {
                                    $message->attach($attachment);
                                }
                            });
                        }
                    }
                }
            }
        });
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_notifications', 'notification_id', 'user_id')->withTimestamps();
    }

    public function campaign()
    {
        return $this->belongsTo(Campaign::class,'campaign_id');
    }

    public function attachments(): MorphMany
    {
        return $this->morphMany(Attachment::class, 'parentable');
    }

    public function hasAttachments()
    {
        return $this->attachments()->count() > 0;
    }

    public function hasBody()
    {
        return $this->description || $this->attachments()->exists();
    }
}
