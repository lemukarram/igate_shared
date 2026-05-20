<x-filament-panels::page>
    <div class="space-y-6">
        <div class="p-6 bg-white rounded-xl shadow-sm border border-gray-100">
            <h2 class="text-xl font-bold mb-4">Base URL</h2>
            <code class="p-2 bg-gray-100 rounded text-blue-600 font-mono text-sm">{{ url('/api/v1') }}</code>
        </div>

        <h1 class="text-2xl font-bold text-gray-800 mb-6">Authentication</h1>

        <!-- Signup -->
        <div class="p-6 bg-white rounded-xl shadow-sm border border-gray-100">
            <div class="flex flex-wrap items-center gap-3 mb-4">
                <span class="px-2 py-1 bg-green-100 text-green-700 rounded text-xs font-bold">POST</span>
                <code class="text-sm font-bold text-gray-800 bg-gray-50 px-2 py-1 rounded">/register</code>
                <h2 class="text-lg font-bold text-gray-700 ml-auto">Signup (Register)</h2>
            </div>
            <p class="text-gray-600 mb-4">Create a new client account.</p>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h3 class="font-bold mb-2 text-sm text-gray-500 uppercase">Payload</h3>
                    <pre class="p-4 bg-gray-900 text-green-400 rounded-lg text-xs overflow-x-auto">
{
  "name": "John Doe",
  "email": "john@example.com",
  "password": "password123",
  "password_confirmation": "password123",
  "phone": "+966500000000"
}</pre>
                </div>
                <div>
                    <h3 class="font-bold mb-2 text-sm text-gray-500 uppercase">Response (201)</h3>
                    <pre class="p-4 bg-gray-900 text-blue-400 rounded-lg text-xs overflow-x-auto">
{
  "status": "success",
  "message": "Registration successful",
  "data": {
    "user": { ... },
    "token": "1|..."
  }
}</pre>
                </div>
            </div>
        </div>

        <!-- Signin -->
        <div class="p-6 bg-white rounded-xl shadow-sm border border-gray-100">
            <div class="flex flex-wrap items-center gap-3 mb-4">
                <span class="px-2 py-1 bg-green-100 text-green-700 rounded text-xs font-bold">POST</span>
                <code class="text-sm font-bold text-gray-800 bg-gray-50 px-2 py-1 rounded">/login</code>
                <h2 class="text-lg font-bold text-gray-700 ml-auto">Signin (Login)</h2>
            </div>
            <p class="text-gray-600 mb-4">Authenticate and get a Sanctum token.</p>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h3 class="font-bold mb-2 text-sm text-gray-500 uppercase">Payload</h3>
                    <pre class="p-4 bg-gray-900 text-green-400 rounded-lg text-xs overflow-x-auto">
{
  "email": "john@example.com",
  "password": "password123"
}</pre>
                </div>
                <div>
                    <h3 class="font-bold mb-2 text-sm text-gray-500 uppercase">Response (200)</h3>
                    <pre class="p-4 bg-gray-900 text-blue-400 rounded-lg text-xs overflow-x-auto">
{
  "status": "success",
  "message": "Login successful",
  "data": {
    "user": { ... },
    "token": "2|..."
  }
}</pre>
                </div>
            </div>
        </div>

        <!-- Forgot Password -->
        <div class="p-6 bg-white rounded-xl shadow-sm border border-gray-100">
            <div class="flex flex-wrap items-center gap-3 mb-4">
                <span class="px-2 py-1 bg-green-100 text-green-700 rounded text-xs font-bold">POST</span>
                <code class="text-sm font-bold text-gray-800 bg-gray-50 px-2 py-1 rounded">/forgot-password</code>
                <h2 class="text-lg font-bold text-gray-700 ml-auto">Forgot Password</h2>
            </div>
            <p class="text-gray-600 mb-4">Send a password reset link to the user's email.</p>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h3 class="font-bold mb-2 text-sm text-gray-500 uppercase">Payload</h3>
                    <pre class="p-4 bg-gray-900 text-green-400 rounded-lg text-xs overflow-x-auto">
{
  "email": "john@example.com"
}</pre>
                </div>
                <div>
                    <h3 class="font-bold mb-2 text-sm text-gray-500 uppercase">Response (200)</h3>
                    <pre class="p-4 bg-gray-900 text-blue-400 rounded-lg text-xs overflow-x-auto">
{
  "status": "success",
  "message": "If an account with that email exists, we have sent a password reset link.",
  "data": null
}</pre>
                </div>
            </div>
        </div>

        <!-- Reset Password -->
        <div class="p-6 bg-white rounded-xl shadow-sm border border-gray-100">
            <div class="flex flex-wrap items-center gap-3 mb-4">
                <span class="px-2 py-1 bg-green-100 text-green-700 rounded text-xs font-bold">POST</span>
                <code class="text-sm font-bold text-gray-800 bg-gray-50 px-2 py-1 rounded">/reset-password</code>
                <h2 class="text-lg font-bold text-gray-700 ml-auto">Reset Password</h2>
            </div>
            <p class="text-gray-600 mb-4">Update the password using the token received in email.</p>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h3 class="font-bold mb-2 text-sm text-gray-500 uppercase">Payload</h3>
                    <pre class="p-4 bg-gray-900 text-green-400 rounded-lg text-xs overflow-x-auto">
{
  "email": "john@example.com",
  "token": "reset-token-here",
  "password": "newpassword123",
  "password_confirmation": "newpassword123"
}</pre>
                </div>
                <div>
                    <h3 class="font-bold mb-2 text-sm text-gray-500 uppercase">Response (200)</h3>
                    <pre class="p-4 bg-gray-900 text-blue-400 rounded-lg text-xs overflow-x-auto">
{
  "status": "success",
  "message": "Password reset successful",
  "data": null
}</pre>
                </div>
            </div>
        </div>

        <!-- Logout -->
        <div class="p-6 bg-white rounded-xl shadow-sm border border-gray-100">
            <div class="flex flex-wrap items-center gap-3 mb-4">
                <span class="px-2 py-1 bg-red-100 text-red-700 rounded text-xs font-bold">POST</span>
                <code class="text-sm font-bold text-gray-800 bg-gray-50 px-2 py-1 rounded">/logout</code>
                <span class="px-2 py-1 bg-blue-100 text-blue-700 rounded text-[10px] uppercase font-bold">Auth Required</span>
                <h2 class="text-lg font-bold text-gray-700 ml-auto">Logout</h2>
            </div>
            <p class="text-gray-600 mb-4">Revoke the current authentication token.</p>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h3 class="font-bold mb-2 text-sm text-gray-500 uppercase">Headers</h3>
                    <pre class="p-4 bg-gray-900 text-yellow-400 rounded-lg text-xs overflow-x-auto">
Authorization: Bearer {token}
Accept: application/json</pre>
                </div>
                <div>
                    <h3 class="font-bold mb-2 text-sm text-gray-500 uppercase">Response (200)</h3>
                    <pre class="p-4 bg-gray-900 text-blue-400 rounded-lg text-xs overflow-x-auto">
{
  "status": "success",
  "message": "Logout successful",
  "data": null
}</pre>
                </div>
            </div>
        </div>

        <div class="border-t border-gray-200 my-8"></div>
        <h1 class="text-2xl font-bold text-gray-800 mb-6">Settings</h1>

        <!-- General Settings -->
        <div class="p-6 bg-white rounded-xl shadow-sm border border-gray-100">
            <div class="flex flex-wrap items-center gap-3 mb-4">
                <span class="px-2 py-1 bg-gray-100 text-gray-700 rounded text-xs font-bold">GET</span>
                <code class="text-sm font-bold text-gray-800 bg-gray-50 px-2 py-1 rounded">/settings/general</code>
                <h2 class="text-lg font-bold text-gray-700 ml-auto">General Settings</h2>
            </div>
            <p class="text-gray-600 mb-4">Get platform-wide general settings.</p>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h3 class="font-bold mb-2 text-sm text-gray-500 uppercase">Response (200)</h3>
                    <pre class="p-4 bg-gray-900 text-blue-400 rounded-lg text-xs overflow-x-auto">
{
  "status": "success",
  "data": {
    "site_name": "iGate",
    "contact_email": "info@igate.com",
    "default_currency": "SAR",
    ...
  }
}</pre>
                </div>
            </div>
        </div>

        <!-- Invoice Settings -->
        <div class="p-6 bg-white rounded-xl shadow-sm border border-gray-100">
            <div class="flex flex-wrap items-center gap-3 mb-4">
                <span class="px-2 py-1 bg-gray-100 text-gray-700 rounded text-xs font-bold">GET</span>
                <code class="text-sm font-bold text-gray-800 bg-gray-50 px-2 py-1 rounded">/settings/invoice</code>
                <h2 class="text-lg font-bold text-gray-700 ml-auto">Invoice Settings</h2>
            </div>
            <p class="text-gray-600 mb-4">Get settings related to invoicing.</p>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h3 class="font-bold mb-2 text-sm text-gray-500 uppercase">Response (200)</h3>
                    <pre class="p-4 bg-gray-900 text-blue-400 rounded-lg text-xs overflow-x-auto">
{
  "status": "success",
  "data": {
    "invoice_prefix": "INV-",
    "company_address": "...",
    ...
  }
}</pre>
                </div>
            </div>
        </div>

        <div class="border-t border-gray-200 my-8"></div>
        <h1 class="text-2xl font-bold text-gray-800 mb-6">Marketplace & Management</h1>

        <!-- Categories -->
        <div class="p-6 bg-white rounded-xl shadow-sm border border-gray-100">
            <div class="flex flex-wrap items-center gap-3 mb-4">
                <span class="px-2 py-1 bg-gray-100 text-gray-700 rounded text-xs font-bold">GET</span>
                <code class="text-sm font-bold text-gray-800 bg-gray-50 px-2 py-1 rounded">/categories</code>
                <h2 class="text-lg font-bold text-gray-700 ml-auto">List Categories</h2>
            </div>
            <p class="text-gray-600 mb-4">Get all service categories.</p>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h3 class="font-bold mb-2 text-sm text-gray-500 uppercase">Response (200)</h3>
                    <pre class="p-4 bg-gray-900 text-blue-400 rounded-lg text-xs overflow-x-auto">
{
  "status": "success",
  "data": [
    { "id": 1, "name": "Financial Services", "slug": "financial-services" }
  ]
}</pre>
                </div>
            </div>
        </div>

        <!-- Category Detail -->
        <div class="p-6 bg-white rounded-xl shadow-sm border border-gray-100">
            <div class="flex flex-wrap items-center gap-3 mb-4">
                <span class="px-2 py-1 bg-gray-100 text-gray-700 rounded text-xs font-bold">GET</span>
                <code class="text-sm font-bold text-gray-800 bg-gray-50 px-2 py-1 rounded">/categories/{id}</code>
                <h2 class="text-lg font-bold text-gray-700 ml-auto">Category Detail</h2>
            </div>
            <p class="text-gray-600 mb-4">Get category details with its services.</p>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h3 class="font-bold mb-2 text-sm text-gray-500 uppercase">Response (200)</h3>
                    <pre class="p-4 bg-gray-900 text-blue-400 rounded-lg text-xs overflow-x-auto">
{
  "status": "success",
  "data": {
    "id": 1,
    "name": "Financial Services",
    "services": [
      { "id": 5, "name": "VAT Filing", ... }
    ]
  }
}</pre>
                </div>
            </div>
        </div>

        <!-- Services -->
        <div class="p-6 bg-white rounded-xl shadow-sm border border-gray-100">
            <div class="flex flex-wrap items-center gap-3 mb-4">
                <span class="px-2 py-1 bg-gray-100 text-gray-700 rounded text-xs font-bold">GET</span>
                <code class="text-sm font-bold text-gray-800 bg-gray-50 px-2 py-1 rounded">/services</code>
                <h2 class="text-lg font-bold text-gray-700 ml-auto">List Services</h2>
            </div>
            <p class="text-gray-600 mb-4">Get all services. Use <code>?category_id=1</code> to filter or <code>?include_subtasks=1</code> to include subtasks.</p>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h3 class="font-bold mb-2 text-sm text-gray-500 uppercase">Response (200)</h3>
                    <pre class="p-4 bg-gray-900 text-blue-400 rounded-lg text-xs overflow-x-auto">
{
  "status": "success",
  "data": [
    { "id": 1, "name": "VAT Filing", "category_id": 1, ... }
  ]
}</pre>
                </div>
            </div>
        </div>

        <!-- Service Detail -->
        <div class="p-6 bg-white rounded-xl shadow-sm border border-gray-100">
            <div class="flex flex-wrap items-center gap-3 mb-4">
                <span class="px-2 py-1 bg-gray-100 text-gray-700 rounded text-xs font-bold">GET</span>
                <code class="text-sm font-bold text-gray-800 bg-gray-50 px-2 py-1 rounded">/services/{id}</code>
                <h2 class="text-lg font-bold text-gray-700 ml-auto">Service Detail</h2>
            </div>
            <p class="text-gray-600 mb-4">Get detailed information about a service including subtasks.</p>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h3 class="font-bold mb-2 text-sm text-gray-500 uppercase">Response (200)</h3>
                    <pre class="p-4 bg-gray-900 text-blue-400 rounded-lg text-xs overflow-x-auto">
{
  "status": "success",
  "data": {
    "id": 1,
    "name": "VAT Filing",
    "subtasks": ["Data entry", "Tax calculation"],
    ...
  }
}</pre>
                </div>
            </div>
        </div>

        <!-- Service Providers -->
        <div class="p-6 bg-white rounded-xl shadow-sm border border-gray-100">
            <div class="flex flex-wrap items-center gap-3 mb-4">
                <span class="px-2 py-1 bg-gray-100 text-gray-700 rounded text-xs font-bold">GET</span>
                <code class="text-sm font-bold text-gray-800 bg-gray-50 px-2 py-1 rounded">/services/{id}/providers</code>
                <h2 class="text-lg font-bold text-gray-700 ml-auto">Service Providers</h2>
            </div>
            <p class="text-gray-600 mb-4">List providers offering this specific service.</p>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h3 class="font-bold mb-2 text-sm text-gray-500 uppercase">Response (200)</h3>
                    <pre class="p-4 bg-gray-900 text-blue-400 rounded-lg text-xs overflow-x-auto">
{
  "status": "success",
  "data": {
    "service": { ... },
    "providers": [
      { "id": 10, "company_name": "Expert Tax Co", "monthly_price": 500, ... }
    ]
  }
}</pre>
                </div>
            </div>
        </div>

        <!-- Provider Detail -->
        <div class="p-6 bg-white rounded-xl shadow-sm border border-gray-100">
            <div class="flex flex-wrap items-center gap-3 mb-4">
                <span class="px-2 py-1 bg-gray-100 text-gray-700 rounded text-xs font-bold">GET</span>
                <code class="text-sm font-bold text-gray-800 bg-gray-50 px-2 py-1 rounded">/providers/{id}</code>
                <h2 class="text-lg font-bold text-gray-700 ml-auto">Provider Detail</h2>
            </div>
            <p class="text-gray-600 mb-4">Get detailed information about a service provider.</p>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h3 class="font-bold mb-2 text-sm text-gray-500 uppercase">Response (200)</h3>
                    <pre class="p-4 bg-gray-900 text-blue-400 rounded-lg text-xs overflow-x-auto">
{
  "status": "success",
  "data": {
    "id": 10,
    "name": "Expert Tax Co",
    "profile": { ... },
    "services": [ ... ]
  }
}</pre>
                </div>
            </div>
        </div>

        <!-- Companies -->
        <div class="p-6 bg-white rounded-xl shadow-sm border border-gray-100">
            <div class="flex flex-wrap items-center gap-3 mb-4">
                <span class="px-2 py-1 bg-gray-100 text-gray-700 rounded text-xs font-bold">GET</span>
                <code class="text-sm font-bold text-gray-800 bg-gray-50 px-2 py-1 rounded">/companies</code>
                <span class="px-2 py-1 bg-blue-100 text-blue-700 rounded text-[10px] uppercase font-bold">Auth Required</span>
                <h2 class="text-lg font-bold text-gray-700 ml-auto">My Companies</h2>
            </div>
            <p class="text-gray-600 mb-4">List all companies registered by the client.</p>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h3 class="font-bold mb-2 text-sm text-gray-500 uppercase">Response (200)</h3>
                    <pre class="p-4 bg-gray-900 text-blue-400 rounded-lg text-xs overflow-x-auto">
{
  "status": "success",
  "data": [
    { "id": 5, "name": "Acme Corp", ... }
  ]
}</pre>
                </div>
            </div>
        </div>

        <div class="p-6 bg-white rounded-xl shadow-sm border border-gray-100">
            <div class="flex flex-wrap items-center gap-3 mb-4">
                <span class="px-2 py-1 bg-gray-100 text-gray-700 rounded text-xs font-bold">GET</span>
                <code class="text-sm font-bold text-gray-800 bg-gray-50 px-2 py-1 rounded">/companies/{id}</code>
                <span class="px-2 py-1 bg-blue-100 text-blue-700 rounded text-[10px] uppercase font-bold">Auth Required</span>
                <h2 class="text-lg font-bold text-gray-700 ml-auto">Company Detail</h2>
            </div>
            <p class="text-gray-600 mb-4">Get detailed information about a specific company.</p>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h3 class="font-bold mb-2 text-sm text-gray-500 uppercase">Response (200)</h3>
                    <pre class="p-4 bg-gray-900 text-blue-400 rounded-lg text-xs overflow-x-auto">
{
  "status": "success",
  "data": { "id": 5, "name": "Acme Corp", ... }
}</pre>
                </div>
            </div>
        </div>

        <div class="p-6 bg-white rounded-xl shadow-sm border border-gray-100">
            <div class="flex flex-wrap items-center gap-3 mb-4">
                <span class="px-2 py-1 bg-green-100 text-green-700 rounded text-xs font-bold">POST</span>
                <code class="text-sm font-bold text-gray-800 bg-gray-50 px-2 py-1 rounded">/companies</code>
                <span class="px-2 py-1 bg-blue-100 text-blue-700 rounded text-[10px] uppercase font-bold">Auth Required</span>
                <h2 class="text-lg font-bold text-gray-700 ml-auto">Create Company</h2>
            </div>
            <p class="text-gray-600 mb-4">Register a new business entity for the client.</p>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h3 class="font-bold mb-2 text-sm text-gray-500 uppercase">Payload</h3>
                    <pre class="p-4 bg-gray-900 text-green-400 rounded-lg text-xs overflow-x-auto">
{
  "name": "Acme Corp",
  "registration_number": "1010XXXXXX",
  "industry": "Tech",
  "about": "..."
}</pre>
                </div>
                <div>
                    <h3 class="font-bold mb-2 text-sm text-gray-500 uppercase">Response (201)</h3>
                    <pre class="p-4 bg-gray-900 text-blue-400 rounded-lg text-xs overflow-x-auto">
{
  "status": "success",
  "message": "Company created",
  "data": { "id": 5, "name": "Acme Corp", ... }
}</pre>
                </div>
            </div>
        </div>

        <!-- Projects -->
        <div class="p-6 bg-white rounded-xl shadow-sm border border-gray-100">
            <div class="flex flex-wrap items-center gap-3 mb-4">
                <span class="px-2 py-1 bg-gray-100 text-gray-700 rounded text-xs font-bold">GET</span>
                <code class="text-sm font-bold text-gray-800 bg-gray-50 px-2 py-1 rounded">/projects</code>
                <span class="px-2 py-1 bg-blue-100 text-blue-700 rounded text-[10px] uppercase font-bold">Auth Required</span>
                <h2 class="text-lg font-bold text-gray-700 ml-auto">My Projects</h2>
            </div>
            <p class="text-gray-600 mb-4">List all active and historical projects for the authenticated client.</p>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h3 class="font-bold mb-2 text-sm text-gray-500 uppercase">Response (200)</h3>
                    <pre class="p-4 bg-gray-900 text-blue-400 rounded-lg text-xs overflow-x-auto">
{
  "status": "success",
  "data": [
    { "id": 10, "status": "in_progress", "service": { ... }, "company": { ... } }
  ]
}</pre>
                </div>
            </div>
        </div>

        <div class="p-6 bg-white rounded-xl shadow-sm border border-gray-100">
            <div class="flex flex-wrap items-center gap-3 mb-4">
                <span class="px-2 py-1 bg-gray-100 text-gray-700 rounded text-xs font-bold">GET</span>
                <code class="text-sm font-bold text-gray-800 bg-gray-50 px-2 py-1 rounded">/projects/{id}</code>
                <span class="px-2 py-1 bg-blue-100 text-blue-700 rounded text-[10px] uppercase font-bold">Auth Required</span>
                <h2 class="text-lg font-bold text-gray-700 ml-auto">Project Detail</h2>
            </div>
            <p class="text-gray-600 mb-4">Get comprehensive project details including tasks, documents, and messages.</p>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h3 class="font-bold mb-2 text-sm text-gray-500 uppercase">Response (200)</h3>
                    <pre class="p-4 bg-gray-900 text-blue-400 rounded-lg text-xs overflow-x-auto">
{
  "status": "success",
  "data": {
    "id": 10,
    "status": "in_progress",
    "tasks": [ ... ],
    "documents": [ ... ],
    "messages": [ ... ]
  }
}</pre>
                </div>
            </div>
        </div>
    </div>
</x-filament-panels::page>
