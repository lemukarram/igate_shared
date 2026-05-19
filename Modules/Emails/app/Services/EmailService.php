<?php

namespace Modules\Emails\Services;

use Modules\Emails\Models\EmailTemplate;
use Modules\Emails\Emails\DynamicEmail;
use Illuminate\Support\Facades\Mail;

class EmailService
{
    /**
     * Send an email using a dynamic template.
     *
     * @param string $eventKey
     * @param string $to
     * @param array $replacements
     */
    public function send(string $eventKey, string $to, array $replacements = []): void
    {
        $template = EmailTemplate::where('event_key', $eventKey)->first();

        if (!$template || !$template->is_active) {
            return;
        }

        $subject = $template->subject;
        $body = $template->body;

        foreach ($replacements as $key => $value) {
            $subject = str_replace($key, $value, $subject);
            $body = str_replace($key, $value, $body);
        }

        Mail::to($to)->send(new DynamicEmail($subject, $body));
    }

    /**
     * Send Signup Activation Email.
     *
     * @param string $email
     * @param string $userName
     * @param string $activationLink
     */
    public function sendSignupActivation(string $email, string $userName, string $activationLink): void
    {
        $this->send('signup_activation', $email, [
            '[USER_NAME]' => $userName,
            '[ACTIVATION_LINK]' => $activationLink,
        ]);
    }

    /**
     * Send Forgot Password Email.
     *
     * @param string $email
     * @param string $userName
     * @param string $resetLink
     */
    public function sendForgotPassword(string $email, string $userName, string $resetLink): void
    {
        $this->send('forgot_password', $email, [
            '[USER_NAME]' => $userName,
            '[RESET_LINK]' => $resetLink,
        ]);
    }

    /**
     * Send New Service Request Email.
     *
     * @param string $email
     * @param string $recipientName
     * @param string $serviceName
     * @param string $projectName
     */
    public function sendNewServiceRequest(string $email, string $recipientName, string $serviceName, string $projectName): void
    {
        $this->send('new_service_request', $email, [
            '[RECIPIENT_NAME]' => $recipientName,
            '[SERVICE_NAME]' => $serviceName,
            '[PROJECT_NAME]' => $projectName,
        ]);
    }

    /**
     * Send Project Status Update Email.
     *
     * @param string $email
     * @param string $clientName
     * @param string $projectName
     * @param string $newStatus
     */
    public function sendProjectStatusUpdate(string $email, string $clientName, string $projectName, string $newStatus): void
    {
        $this->send('project_status_update', $email, [
            '[CLIENT_NAME]' => $clientName,
            '[PROJECT_NAME]' => $projectName,
            '[NEW_STATUS]' => $newStatus,
        ]);
    }

    /**
     * Send Invoice Generated Email.
     *
     * @param string $email
     * @param string $clientName
     * @param string $invoiceNumber
     * @param string $invoiceAmount
     * @param string $invoiceLink
     */
    public function sendInvoiceGenerated(string $email, string $clientName, string $invoiceNumber, string $invoiceAmount, string $invoiceLink): void
    {
        $this->send('invoice_generated', $email, [
            '[CLIENT_NAME]' => $clientName,
            '[INVOICE_NUMBER]' => $invoiceNumber,
            '[INVOICE_AMOUNT]' => $invoiceAmount,
            '[INVOICE_LINK]' => $invoiceLink,
        ]);
    }
}
