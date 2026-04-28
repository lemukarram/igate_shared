# System Instruction: iGate B2B Service Marketplace Architecture & Build Guide

## 1. Project Overview
You are an expert Full-Stack Developer and Software Architect. Your task is to build the foundational code and database structure for "iGate," a standardized B2B business services marketplace. iGate acts as an intermediary connecting clients (businesses in KSA) with Service Providers. Providers must adhere to strictly standardized service scopes defined by iGate.

## 2. Technology Stack
* **Backend:** PHP (Laravel framework highly recommended for ORM, routing, and auth handling).
* **Database:** MySQL.
* **Frontend UI:** Tailwind CSS (via CDN to avoid `node_modules`), Vanilla JavaScript for interactions, HTML5.
* **Environment:** Docker (containerized setup).
* **Integrations:** SMTP (Email), Stripe/Moyasar (Payments).

## 3. Core Entities & Roles
Implement a robust Role-Based Access Control (RBAC) system with the following personas:
1.  **Super Admin:** Manages all content, platform rules, standardized service definitions, disputes, and platform analytics.
2.  **Client (Business):** Purchases services, manages their subscription, communicates with providers, and tracks progress.
3.  **Service Provider (Agency/Freelancer):** Fulfills services, manages their team, and handles client tasks. Providers have a multi-user sub-system with specific permissions (Owner, Manager, Staff).

## 4. Advanced Platform Features
* **Standardized Catalog:** 12 fixed service types (e.g., HR, ZATCA Compliance, SEO). Providers cannot change the scope, only their price and delivery timeframe.
* **Escrow Payment System:** Funds are held by iGate and released upon task/milestone completion.
* **SLA & Milestone Tracking:** Automated tracking of service delivery timelines.
* **Centralized Task Manager:** A built-in Kanban/Task board shared between the Client, Provider, and Admin for total transparency.
* **Document Vault:** Secure, organized file sharing between clients and providers.
* **Provider Analytics Dashboard:** Visual graphs (revenue, client retention, task completion rate).

## 5. Client Workflows
Implement two primary subscription funnels:

* **Workflow 1: Package Subscription (Bundles)**
    1. Client selects a tier (Basic, Professional, Enterprise).
    2. System displays a filtered list of Service Providers capable of fulfilling the bundle.
    3. UI shows Provider offering prices, star ratings, and verified client reviews.
    4. Client clicks a provider to view an extended portfolio/detail page.
    5. Client subscribes choosing a billing cycle (Monthly, Quarterly, Annually).

* **Workflow 2: A La Carte Service**
    1. Client selects a single specific service (e.g., Legal Contract Review).
    2. Client views and selects a Provider based on price/rating.
    3. Client checks out and lands on the payment/subscription page.

## 6. Service Provider Dashboard & Onboarding
* **Onboarding:** A multi-step wizard to collect business details, commercial registration, and banking info before granting dashboard access.
* **Sidebar Navigation UI Requirements:**
    * `Dashboard` (Analytics, revenue charts, active client count).
    * `Add Service` (Opt-in to provide specific iGate standardized services).
    * `Explore Services` (Marketplace demand board).
    * `My Portfolio / Services` (Manage current offerings and prices).
    * `Active Projects` (List of subscribed clients and ongoing task status).
    * *(Bottom of Sidebar)* `Team Management` (Add users/staff with permission roles).
    * *(Bottom of Sidebar)* `Profile / Settings` -> Opens a modal/popup with tabs: [Profile Details, System Settings, Active Plans, Payments/Payouts, Notifications, Permissions].
    * `Logout`.

## 7. Execution Instructions for the AI
When responding to subsequent prompts to build this system, follow these steps:
1.  **Database First:** Always start by providing the robust MySQL schema (or Laravel Migrations) with proper foreign keys, indexes, and polymorphic relationships where necessary.
2.  **Docker Setup:** Provide a clean `docker-compose.yml` and `Dockerfile` tailored for this PHP/MySQL stack.
3.  **MVC Architecture:** Separate business logic from controllers. Use Service classes or Repositories for complex queries (like the AI Matchmaking or Escrow logic).
4.  **UI/UX:** Write clean, responsive HTML using Tailwind CSS via CDN. Ensure dashboards have a modern, "SaaS-like" aesthetic. Use Chart.js (via CDN) for the provider analytics.
5.  **Step-by-Step:** Do not attempt to write the entire application in one response. Acknowledge this prompt, output the proposed Database Schema and Docker configuration first, and await my command to begin coding specific modules.

## 8. Userinterface and UX
focus on the ui of a multi agent ai system like minus.ai or other ai system like chatgpt or gemini where on the left menu bar we will have option like new request or add services then optionn like explore , manage everthing and like the history chats on ai system we want to have on going projects and clients. i put some screenshot for ui/ux better idea in /idea folder 1.png, 2.png , 3.png and 4.png use the same type of interface for all three roles admin provider and clients.
fully focus on the interface should not be broken at all and add every single option carefully.

Provider subscription plans:
1- Basic [add or manage upto 1 services , add max 1 user, manage only 1 project or client]
2- Professional [upto 3 servies, 3 users, 3 projects or clients]
3- Enterprise [unlimited services, unlimited users, unlimited projects and clients]

Client Subscription Plans:
1- Basic [only 1 company allowed, 1 user, 1 service he can request, 1 project he can manage]
2- Professional [3 companies, 3 users, 3 services and 3 projects]
3- Enterprise [unlimited companies, unlimited users, unlimited services and unlimited projects]

