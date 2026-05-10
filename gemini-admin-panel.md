# Role: Expert Laravel & FilamentPHP Architect
You are tasked with building the "Admin" module for a B2B Services Marketplace (iGate). The application uses Laravel 11, MySQL, and a Modular Monolith architecture via `nWidart/laravel-modules`.

We are using **Filament v3** as the admin panel. 

## 1. Architectural Constraints
* **Modular Location:** All admin-related code (Controllers, Resources, Pages, Providers) MUST live inside `Modules/Admin`. 
* **Shared Authentication:** The system uses a single, shared frontend login screen. Do NOT use Filament's default login page. Instruct the system to use the default `web` auth guard. If an authenticated user has the 'Super Admin' role, they are redirected to `/admin`.
* **Access Control:** The `User` model must implement Filament's `FilamentUser` interface. The `canAccessPanel()` method must restrict access to users with the 'Super Admin' or 'Admin' roles.

## 2. System Entities & Role Hierarchy
The database and Admin Panel must handle a complex, hierarchical organization structure. The Admin must be able to view, manage, and override all of the following:

**A. Client Organization Hierarchy:**
* **Main Client:** Owns the account. Can create/manage multiple `Companies`. Can subscribe these companies to `Services`, manage `Projects`, and add sub-users.
* **Client Staff / Manager:** Sub-users under a Main Client. To the outside world, they act as the "Client". Internally, they have granular permissions (View/Edit) restricted to specific `Companies`, `Projects`, or billing settings.

**B. Service Provider Hierarchy:**
* **Main Service Provider:** Owns the agency account. Manages their offered `Services`, handles `Clients`, and oversees all `Projects`. Can add sub-users.
* **Provider Staff / Manager:** Sub-users under a Main Provider. To the outside world, they act as the "Provider". Internally, they have granular access restricted to specific assigned `Clients`, `Projects`, or `Services`.

## 3. Required Filament Resources & Plugins
Please generate the exact terminal commands and PHP code/configurations for the following Filament components. Utilize `spatie/laravel-permission`, `spatie/laravel-settings`, and `bezhansalleh/filament-shield`.

### A. Organization & Identity Management
* **Organization/CompanyResource:** Manage Client Companies and Provider Agencies. Include Relation Managers to view all associated sub-users, active subscriptions, and projects.
* **UserResource:** Full CRUD for all platform users. Include filters for 'Type' (Main Client, Client Staff, Main Provider, Provider Staff, Admin). For Staff roles, show a field linking them to their Parent User/Organization. Include an "Impersonate" action.
* **Roles & Permissions Page:** Integrated via Filament Shield. The Admin must be able to see and manage the granular permissions that Main Clients/Providers assign to their staff.

### B. Marketplace & Project Operations
* **ServiceResource:** Manage the standardized iGate services.
* **SubscriptionResource:** Track which Client Company is subscribed to which Service, billing cycles, and status.
* **ProjectResource:** Oversee all active projects. Must link to the Client Company, the Subscribed Service, and the Assigned Provider. Include Relation Managers for:
    * **Tasks/Statuses:** What step the project is on.
    * **Communications/Logs:** A read-only audit trail of messages between the Client and Provider for verification and dispute resolution.
* **DisputeManager (Custom Page):** A dedicated page listing projects marked "Disputed", with action buttons for the Admin to "Split Funds", "Refund Client", or "Release to Provider".

### C. Financial & Escrow Management
* **EscrowLedgerResource:** Read-only ledger showing funds held in Stripe/Moyasar per project.
* **TransactionResource:** Log of platform payouts and subscriptions. Include Header Widgets for "Total Revenue", "Escrow Holding", and "Pending Payouts".

### D. System Settings & Communications
* **GlobalBroadcast (Custom Page):** Allow the Admin to push an email/notification to specific segments (e.g., "All Provider Managers" or "All Main Clients").
* **GeneralSettings (Settings Page):** Manage Site Name, Default Currency, Platform Fee %, and Translations (English/Arabic).

## 4. Execution Protocol
Execute this task in the following strict order. Wait for my confirmation after each step:

**Step 1: Database Architecture:** Provide the required migrations (Users, Companies, Projects, Subscriptions, Teams/Parent_IDs for hierarchy) to support the complex Client/Provider staff relationships.
**Step 2: Setup & Auth:** Provide terminal commands to create the `Admin` module, install Filament, and configure the `AdminPanelProvider` to bypass the default login and use the shared auth.
**Step 3: Identity & Organizations:** Provide the code for UserResource, CompanyResource, and Shield setup.
**Step 4: Marketplace Operations:** Provide the Filament Resources for Subscriptions, Projects (with Task/Comm logs), and the Dispute Custom Page.
**Step 5: Finances & Settings:** Provide the Escrow Ledger, Settings Page, and Broadcast Page.

Begin with Step 1.