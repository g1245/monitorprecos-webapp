<?php

namespace App\Services;

use App\Mail\AdminNewPriceAlertMail;
use App\Mail\AdminNewUserMail;
use App\Models\User;
use App\Models\UserWishProduct;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

/**
 * AdminNotificationService
 *
 * Centralizes asynchronous admin email notifications. All dispatches are
 * queued so they never block the user-facing request lifecycle.
 */
class AdminNotificationService
{
    /**
     * Notify the admin that a new user account was created.
     */
    public function notifyNewUser(User $user): void
    {
        $adminEmail = config('mail.admin_email');

        if (empty($adminEmail)) {
            Log::warning('AdminNotificationService: ADMIN_EMAIL is not configured.');
            return;
        }

        Mail::to($adminEmail)->queue(new AdminNewUserMail($user));
    }

    /**
     * Notify the admin that a new price alert was created.
     *
     * Only dispatches when the wish has a target_price set.
     */
    public function notifyNewPriceAlert(UserWishProduct $wish): void
    {
        if (! $wish->hasPriceAlert()) {
            return;
        }

        $adminEmail = config('mail.admin_email');

        if (empty($adminEmail)) {
            Log::warning('AdminNotificationService: ADMIN_EMAIL is not configured.');
            return;
        }

        $wish->loadMissing(['user', 'product']);

        Mail::to($adminEmail)->queue(new AdminNewPriceAlertMail($wish));
    }
}
