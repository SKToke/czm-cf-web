<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class CampaignUpdate extends Model
{
    use SoftDeletes;

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

    public function getCustomMessage()
    {
        $msg = "";
        if (!is_null($this->message)) {
            $msg = $this->message;
        }
        if (!is_null($this->disbursed_amount)) {
            $msg = $msg . '<p class="mt-2">' . $this->disbursed_amount . "Tk has been disbursed." . '</p>';
        }
        return $msg;
    }

    public function getFormattedAttachments()
    {
        $attachments = $this->attachments;

        if ($attachments->count() > 0) {
            $html = "Attachments: " . '<ul>';
            foreach ($attachments as $attachment) {
                $filePath = Storage::disk('admin')->url($attachment->file);
                $html .= "<li><a href='{$filePath}' target='_blank'>{$attachment->title}</a></li>";
            }
            $html .= '</ul>';
            return $html;
        }

        return 'No attachments';
    }

    protected static function boot()
    {
        parent::boot();

        static::saved(function ($campaignUpdate) {
            if (!is_null($campaignUpdate->disbursed_amount) ) {
                $campaignUpdate->campaign->updateOrCreateReport();
            }
        });
    }
}
