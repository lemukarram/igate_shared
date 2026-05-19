# Role: Expert Laravel Architect
You are tasked with building a dedicated "Emails" module for the iGate B2B Marketplace using `nWidart/laravel-modules`. This module will manage and send all transactional emails using Laravel's native SMTP mailer.

## 1. Technical Architecture & Database
* **Module Name:** `Modules/Emails`
* **Database Table:** Create a migration for `email_templates`.
    * Columns: `id`, `event_key` (string, unique, e.g., 'signup_activation'), `is_active` (boolean, default true), `subject` (string), `body` (text/longtext for HTML), `variables` (string, helper text for admins to know what they can use like [USER_NAME]).
* **Mail Engine:** Create a single, dynamic Laravel Mailable (e.g., `DynamicEmail`) that implements `ShouldQueue`. It should accept the dynamic subject and parsed HTML body from the database.

## 2. Admin Panel Integration (Filament v3)
* **EmailTemplateResource:** Create a Filament resource inside the Admin module.
    * **List View:** Show the Event Key, Subject, and a Toggle button for `is_active`.
    * **Edit View:** Include a rich text editor (Filament's RichEditor) for the email body so Admins can format the text, add links, and use placeholders (e.g., `Hello [NAME]`). 
    * Add a Read-only helper field showing the allowed variables for that specific event so the Admin knows what placeholders they can type.

## 3. Required Email Events
The `EmailService` must have dedicated methods to handle the parsing and sending for these specific events:
1. **Signup Activation:** Welcome email with verification link.
2. **Forgot Password:** Override Laravel's default reset email to use the dynamic template.
3. **New Service Request:** Sent to BOTH the Client (confirmation) and the Provider (new lead notification).
4. **Project Status Update:** Sent to Client when a Provider updates a milestone or task.
5. **Invoice Generated:** Sent to Client with a link to view/download the invoice.

## 4. The Parsing Logic
* The `EmailService` must check if the template `is_active` is true. If false, it simply returns and does not send the email.
* It must fetch the template by `event_key` and perform a string replacement (e.g., replacing `[PROJECT_NAME]` in the database body with the actual `$project->name`) before passing it to the queued Mailable.

## 5. Execution Protocol
Execute the following steps in order and wait for my confirmation after each:

**Step 1: Database & Seeders:** Provide the `email_templates` migration and a Seeder to populate the initial templates (Signup, Password, Service Request, Project Update, Invoices) with default text and variables.
**Step 2: The Service & Mailable:** Provide the `DynamicEmail` Mailable class (with `ShouldQueue`) and the `EmailService` class containing the parsing and sending logic.
**Step 3: Filament Admin:** Provide the `EmailTemplateResource` code for managing these templates in the backend.
**Step 4: Event Hooks:** Provide examples of how to call the `EmailService` methods from the main application controllers or Laravel Event Listeners.