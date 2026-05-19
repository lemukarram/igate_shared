<?php

namespace Modules\Emails\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Emails\Models\EmailTemplate;

class EmailsDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $templates = [
            [
                'event_key' => 'signup_activation',
                'is_active' => true,
                'subject' => 'Welcome to iGate - Please verify your email',
                'body' => '<p>Hello [USER_NAME],</p><p>Welcome to iGate! Please click the link below to verify your email address:</p><p><a href="[ACTIVATION_LINK]">Verify Email</a></p><p>Thank you,<br>The iGate Team</p>',
                'variables' => '[USER_NAME], [ACTIVATION_LINK]',
            ],
            [
                'event_key' => 'forgot_password',
                'is_active' => true,
                'subject' => 'iGate - Password Reset Request',
                'body' => '<p>Hello [USER_NAME],</p><p>You are receiving this email because we received a password reset request for your account.</p><p><a href="[RESET_LINK]">Reset Password</a></p><p>If you did not request a password reset, no further action is required.</p><p>Thank you,<br>The iGate Team</p>',
                'variables' => '[USER_NAME], [RESET_LINK]',
            ],
            [
                'event_key' => 'new_service_request',
                'is_active' => true,
                'subject' => 'iGate - New Service Request: [SERVICE_NAME]',
                'body' => '<p>Hello [RECIPIENT_NAME],</p><p>A new service request for <strong>[SERVICE_NAME]</strong> has been submitted.</p><p><strong>Project:</strong> [PROJECT_NAME]</p><p>Please log in to your dashboard for more details.</p><p>Thank you,<br>The iGate Team</p>',
                'variables' => '[RECIPIENT_NAME], [SERVICE_NAME], [PROJECT_NAME]',
            ],
            [
                'event_key' => 'project_status_update',
                'is_active' => true,
                'subject' => 'iGate - Project Status Updated',
                'body' => '<p>Hello [CLIENT_NAME],</p><p>The status of your project <strong>[PROJECT_NAME]</strong> has been updated to: <strong>[NEW_STATUS]</strong>.</p><p>Please log in to your dashboard to view the latest updates.</p><p>Thank you,<br>The iGate Team</p>',
                'variables' => '[CLIENT_NAME], [PROJECT_NAME], [NEW_STATUS]',
            ],
            [
                'event_key' => 'invoice_generated',
                'is_active' => true,
                'subject' => 'iGate - New Invoice Available',
                'body' => '<p>Hello [CLIENT_NAME],</p><p>A new invoice <strong>[INVOICE_NUMBER]</strong> for the amount of <strong>[INVOICE_AMOUNT]</strong> has been generated.</p><p><a href="[INVOICE_LINK]">View / Download Invoice</a></p><p>Thank you for doing business with us!</p><p>The iGate Team</p>',
                'variables' => '[CLIENT_NAME], [INVOICE_NUMBER], [INVOICE_AMOUNT], [INVOICE_LINK]',
            ],
        ];

        foreach ($templates as $template) {
            EmailTemplate::updateOrCreate(
                ['event_key' => $template['event_key']],
                $template
            );
        }
    }
}
