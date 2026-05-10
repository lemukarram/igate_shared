# iGate Database Documentation

This document provides a comprehensive overview of the iGate database structure, including tables, fields, relationships, and descriptions.

## 1. User & Access Management

### `users`
Core table for all platform participants (Admins, Clients, Providers).
- `id`: Primary key.
- `name`: Full name of the user.
- `email`: Unique email address.
- `profile_picture`: Path to the user's avatar.
- `phone`: Contact number.
- `role`: user role (`admin`, `client`, `provider`).
- `parent_id`: Foreign key to `users` (self-referencing). Used for hierarchy (Staff/Managers linked to a main account).
- `plan_id`: Foreign key to `plans`. Current subscription tier.
- `notification_settings`: JSON field for user-specific notification preferences.
- `email_verified_at`: Timestamp for email verification.
- `password`: Hashed password.
- `remember_token`: Session token.
- `timestamps`: `created_at` and `updated_at`.

### `provider_profiles`
Extended profile information specifically for Service Providers.
- `id`: Primary key.
- `user_id`: Foreign key to `users` (Relationship: One-to-One).
- `company_name`: Legal name of the provider agency.
- `commercial_registration`: CR number.
- `tax_number`: VAT/Tax ID.
- `bank_name`: Registered bank name.
- `iban`: International Bank Account Number for payouts.
- `bio`: Short description of the agency.
- `logo`: Path to company logo.
- `status`: Verification status (`pending`, `verified`, `rejected`, `active`, `inactive`).
- `onboarding_completed`: Boolean flag for multi-step onboarding.

### `teams`
Allows providers and clients to organize their staff.
- `id`: Primary key.
- `owner_id`: Foreign key to `users` (The person who created the team).
- `name`: Team name.

### `team_members`
Pivot table for users within teams.
- `id`: Primary key.
- `team_id`: Foreign key to `teams`.
- `user_id`: Foreign key to `users`.
- `role`: Member role (`owner`, `manager`, `staff`).
- `is_active`: Boolean status.

---

## 2. Service Management

### `service_categories`
Groupings for standardized services.
- `id`: Primary key.
- `name`: Category name (e.g., Marketing, Legal).
- `slug`: URL-friendly identifier.

### `services`
Standardized service definitions controlled by iGate Admins.
- `id`: Primary key.
- `service_category_id`: Foreign key to `service_categories`.
- `name`: Service title (e.g., ZATCA Compliance).
- `description`: Detailed scope of work.
- `subtasks`: JSON field defining the standard checklist for this service.
- `icon`: Visual representation.

### `provider_services`
The marketplace catalog where providers opt-in to standardized services.
- `id`: Primary key.
- `provider_id`: Foreign key to `users`.
- `service_id`: Foreign key to `services`.
- `price`: Provider's specific price for this service.
- `delivery_time_days`: Expected turnaround time.
- `provider_notes`: Custom notes from the provider.
- `is_active`: Boolean status.

---

## 3. Subscription & Client Management

### `plans`
Defines platform tiers and their limits.
- `id`: Primary key.
- `name`: Tier name (Basic, Professional, Enterprise).
- `type`: Target role (`provider`, `client`).
- `max_services`: Limit on active services.
- `max_users`: Limit on team members.
- `max_projects`: Limit on active engagements.
- `max_companies`: Limit on client business entities.

### `companies`
Business entities owned by a Client.
- `id`: Primary key.
- `client_id`: Foreign key to `users` (Owner).
- `name`: Company name.
- `industry`: Industry sector.
- `registration_number`: CR number.
- `about`: Brief company description.
- `logo`: Company logo path.
- `is_active`: Boolean status.

### `subscriptions`
Tracks active service-specific subscriptions for clients.
- `id`: Primary key.
- `client_id`: Foreign key to `users`.
- `company_id`: Foreign key to `companies`.
- `service_id`: Foreign key to `services`.
- `provider_id`: Foreign key to `users`.
- `plan_name`: Snapshot of the plan tier.
- `billing_cycle`: `monthly`, `quarterly`, `annually`.
- `status`: `active`, `cancelled`, `expired`, `pending`.
- `starts_at`, `ends_at`, `canceled_at`: Timestamps.

---

## 4. Project & Task Management

### `projects`
The core engagement record between a Client and a Provider.
- `id`: Primary key.
- `client_id`: Foreign key to `users`.
- `provider_id`: Foreign key to `users`.
- `service_id`: Foreign key to `services`.
- `provider_service_id`: Foreign key to `provider_services`.
- `company_id`: Foreign key to `companies`.
- `status`: Current state (`pending`, `active`, `completed`, `disputed`, `cancelled`).
- `total_amount`: Total contract value.
- `provider_marked_complete`: Boolean (Provider side action).
- `client_approved`: Boolean (Client side action).
- `mutual_cancellation_requested`: Boolean.
- `cancellation_requested_by`: `client` or `provider`.
- `termination_requested`: Boolean.
- `dispute_reason`, `termination_reason`, `rejection_reason`: Text logs for issues.
- `last_action_by`: Tracks who performed the last critical update.
- `start_date`, `end_date`, `completed_at`, `escrow_released_at`: Timestamps.

### `tasks`
Individual units of work within a project.
- `id`: Primary key.
- `project_id`: Foreign key to `projects`.
- `provider_id`: Foreign key to `users`.
- `team_id`: Foreign key to `teams` (Assigned team).
- `assigned_to`: Foreign key to `users` (Assigned staff).
- `title`: Task name.
- `description`: Instructions.
- `status`: `todo`, `in_progress`, `review`, `done`.
- `priority`: `normal`, `high`, `urgent`.
- `order`: Sorting order for Kanban boards.
- `is_verified`: Boolean (Admin/Client verification).
- `due_date`, `verified_at`: Timestamps.

### `milestones`
Defined progress points linked to escrow release.
- `id`: Primary key.
- `project_id`: Foreign key to `projects`.
- `title`: Milestone name.
- `amount`: Value to be released upon completion.
- `status`: `pending`, `completed`, `released`.
- `due_date`, `completed_at`: Timestamps.

---

## 5. Financials & Escrow

### `payments`
Records of financial transactions.
- `id`: Primary key.
- `project_id`: Foreign key to `projects`.
- `user_id`: Foreign key to `users` (Payer).
- `amount`: Paid amount.
- `payment_method`: Gateway used (Stripe, Moyasar, etc.).
- `transaction_id`: External reference.
- `status`: `pending`, `held_in_escrow`, `released`, `refunded`.

### `escrow_ledgers`
Audit trail for the escrow system.
- `id`: Primary key.
- `project_id`: Foreign key to `projects`.
- `amount`: Movement amount.
- `type`: `credit` (funds in), `debit` (funds out).
- `description`: Reason for movement.

### `release_requests`
Provider requests to release funds from escrow.
- `id`: Primary key.
- `project_id`: Foreign key to `projects`.
- `provider_id`: Foreign key to `users`.
- `amount`: Requested release amount.
- `status`: `pending`, `approved`, `rejected`.
- `notes`: Provider's justification.

---

## 6. Communication & Logs

### `messages`
Public (between Client and Provider) project communication.
- `id`: Primary key.
- `project_id`: Foreign key to `projects`.
- `user_id`: Foreign key to `users` (Sender).
- `message`: Text content.

### `pre_sale_messages`
Communication before a project is officially created.
- `id`: Primary key.
- `client_id`, `provider_id`, `service_id`: Context.
- `sender_id`: The sender.
- `message`: Text content.

### `internal_messages`
Private communication within a Provider/Client team.
- `id`: Primary key.
- `team_id`: Target team.
- `user_id`: Sender.
- `message`: Text content.

### `documents`
Secure file vault.
- `id`: Primary key.
- `project_id`, `task_id`: Context.
- `user_id`: Uploader.
- `name`: Display name.
- `file_path`: Storage path.
- `file_type`, `file_size`: Metadata.
- `is_private`: Visibility flag.

### `reviews`
Post-project feedback.
- `id`: Primary key.
- `project_id`: Context.
- `reviewer_id`, `reviewee_id`: Users involved.
- `rating`: 1-5 stars.
- `comment`: Text feedback.

### `project_histories` / `task_histories`
Full audit logs for changes in projects and tasks (field-level tracking).

---

## 7. System Configuration

### `platform_settings`
Key-value pairs for platform-wide logic.
- `key`, `value`.

### `settings` (Spatie)
Structured settings managed by the `general` group.
- `site_name`, `default_currency`, `platform_fee_percentage`, `default_language`.
