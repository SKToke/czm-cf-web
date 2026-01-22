<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Campaign;
use App\Models\UserNotification;
use App\Traits\HttpResponses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    use HttpResponses;

    protected function getCampaignBaseInfo(Campaign $campaign): array
    {
        return [
            'id' => $campaign->id,
            'campaign_id' => $campaign->campaign_id,
            'slug' => $campaign->slug,
            'title' => $campaign->title,
            'is_donatable' => Campaign::isDonatable($campaign->campaign_id),
            'is_subscribable' => $campaign->isSubscribable(),
            'urgent' => (bool)$campaign->urgency_status,
            'total_collected' => (int)($campaign->getFundCount()),
            'total_allocated' => (int)$campaign->allocated_amount,
            'remaining_days' => (int)$campaign->getRemainingDays(),
            'total_supporters' => (int)$campaign->getTotalSupporters(),
            'has_donation' => count($campaign->getDonations()) > 0,
            'last_donated_at' => $campaign->getFormatttedLastDonationDate(),
            'progress_in_percentage' => (int)$campaign->getFundPercentage(),
            'parent_program_title' => $campaign->program->title,
            'banner' => $campaign->getThumbnailImage(),
            'campaign_share_link' => route('campaign-details', ['slug' => $campaign->slug]),
        ];
    }

    public function userNotifications(Request $request)
    {
        try {
            $user = Auth::user();

            if ($user && $user->active) {

                if ($user->notifications) {
                    $unreadNotifications = $user->getUnreadNotifications()->map(function ($notification) {
                        $campaignInfo = null;
                        if ($notification->campaign) {
                            $campaignInfo = $this->getCampaignBaseInfo($notification->campaign);
                        }
                        return [
                            'notification_id' => $notification->id,
                            'notification_title' => $notification->notification_title,
                            'notification_description' => $notification->notification_description,
                            'campaign' => $campaignInfo,
                            'created_at' => $notification->created_at->format('Y-m-d H:i:s'),
                        ];
                    });

                    $viewedNotifications = $user->getArchivedNotifications()->map(function ($notification) {
                        $campaignInfo = null;
                        if ($notification->campaign) {
                            $campaignInfo = $this->getCampaignBaseInfo($notification->campaign);
                        }
                        return [
                            'notification_id' => $notification->id,
                            'notification_title' => $notification->notification_title,
                            'notification_description' => $notification->notification_description,
                            'campaign' => $campaignInfo,
                            'created_at' => $notification->created_at->format('Y-m-d H:i:s'),
                        ];
                    });
                }

                $data = [
                    'total_unread_notifications' => count($unreadNotifications),
                    'total_viewed_notifications' => count($viewedNotifications),
                    'unread_notifications' => $unreadNotifications,
                    'viewed_notifications' => $viewedNotifications,
                ];

                return $this->success('User Notifications', $data);
            } else {
                return $this->error('Unauthorized', [], 401);
            }
        } catch (\Exception $e) {
            // Return error response for any other exception
            return $this->error('Error occurred: ' . $e->getMessage(), [], 500);
        }
    }

    public function markNotificationAsRead(Request $request)
    {
        try {
            $user = Auth::user();

            if ($user && $user->active) {
                $notification = UserNotification::where([
                    'notification_id' => $request->get('notification_id'),
                    'user_id' => $user->id]
                )->first();

                if ($notification) {
                    $notification->read_at = now();
                    $notification->save();
                }
                return $this->success('Notification marked as read successfully');
            } else {
                return $this->error('Unauthorized', [], 401);
            }
        } catch (\Exception $e) {
            // Return error response for any other exception
            return $this->error('Error occurred: ' . $e->getMessage(), [], 500);
        }
    }
}
