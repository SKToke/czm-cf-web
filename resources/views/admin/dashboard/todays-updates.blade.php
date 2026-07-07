<div class="card top-line-card">
    <div class="card-body">
        <h5 class="card-title">Today's Updates</h5>
    </div>
</div>
<div class="card update-card-dashboard">
    <div class="card-body">
        <table>
            <tr>
                <td>Total Donations Today</td>
                <td>{{ $donations }} Tk</td>
                <td>{{ $donors }} donors</td>
                <td><a href="{{ admin_url('donations') }}">See all</a></td>
            </tr>
            <tr>
                <td>Total Recurring Donation Today</td>
                <td>{{ $recurringDonations }} Tk</td>
                <td>{{ $recurringDonors }} donors</td>
                <td><a href="{{ admin_url('recurring-subscriptions') }}">See all</a></td>
            </tr>
            <tr>
                <td>Total Campaign Subscriptions Today</td>
                <td>-</td>
                <td>{{ $campaignSubscriptions }}</td>
                <td><a href="{{ admin_url('campaign-subscriptions') }}">See all</a></td>
            </tr>
            <tr>
                <td>Total Newsletter Subscriptions Today</td>
                <td>-</td>
                <td>{{ $newsletterSubscriptions }}</td>
                <td><a href="{{ admin_url('newsletter_subscriptions') }}">See all</a></td>
            </tr>
        </table>
    </div>
</div>
