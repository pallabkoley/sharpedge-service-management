SharpEdge Service Management Platform

A Laravel-Based Business Operations, Service Management & Accounting Platform

SharpEdge Service Management is a business application designed to manage service orders, client relationships, payment workflows, document verification, internal tasks, and back-office accounting operations through dedicated role-based panels.

Application Panels

1. Admin Panel

Centralized administration for managing services, clients, staff, orders, payments, and operational workflows.

Key Features

Dashboard & Analytics — Business overview, service statistics, client metrics, and revenue charts.

Service Configuration — Manage service categories, service masters, document requirements, and service-related settings.

Client Management — Maintain client records, client master profiles, bank accounts, cases, compliance records, turnover data, portal credentials, and custom fields.

Order Management — Create and manage service orders, review order details, and handle invoice-related operations.

Payment Management — Review payment submissions, manage receipts, track payment status, and handle payment follow-ups.

Service Selling Analysis — Analyze order volumes, revenue, and service-wise performance.

Document Verification — Review, approve, or reject submitted documents, including bulk verification actions.

Task Management — Assign tasks and verify submitted work.

Workflow Monitoring — Review workflow health and recalculate order workflows when required.

Staff Operations — Manage attendance, leave approvals, leave policies, holidays, and notices.

Support & Integrations — Manage support tickets and partner website records.

2. Marketing Panel

A dedicated workspace for client coordination, service orders, payments, meetings, and follow-ups.

Key Features

Client Management — Create, update, and maintain client records.

Service Order Processing — Create orders through a multi-step workflow, validate coupons, calculate order amounts, and track due payments.

Payment Operations — Record and manage payments, monitor payment status, and access invoice and payment-related information.

Payment Follow-ups — Maintain follow-up records and review follow-up history.

Meeting Management — Schedule meetings and manage meeting requests.

Document Handling — Upload, view, and manage client and order-related documents.

Support Tickets — Create tickets, view ticket details, reply, and update ticket status.

Employee Operations — Manage leave requests, holiday calendars, and notices.

Dashboard Analytics — View client statistics, invoice summaries, recent activities, and chart data.

3. Back Office Panel

A dedicated operational workspace for task execution, document management, client coordination, payment follow-ups, and accounting computations.

Key Features

Accounting & Financial Computations

Computation Management — Create, edit, save, submit, and manage financial computations.

Balance Sheet — Manage balance-sheet entries, update individual items, finalize records, and export PDF reports.

Profit & Loss — Maintain profit-and-loss data, update categories and rows, and finalize computations.

Trading Account — Manage trading-account entries and finalize records.

Appropriation — Save drafts, finalize appropriation records, and export PDF output.

Reconciliation Modules — Work with debtor, creditor, bank, sales, purchase, and asset reconciliations.

Debit/Credit Note Register — Manage registers, update records, maintain breakup details, and finalize entries.

Financial Reports — Access profit-and-loss and balance-sheet reports.

Operations & Document Workflows

Task Management — View assigned tasks, start work, mark waiting status, submit completed work, add remarks, and communicate through task messages.

Task Documents — Upload, download, and replace task-related documents.

Client Documents — View, upload, and manage client documents.

Payment Follow-ups — Record and review payment follow-up information and history.

Support Tickets — View tickets, reply to conversations, and update pending or resolved status.

Employee Operations — Clock in/out, submit leave requests, view the holiday calendar, and access notices.

Partner Websites — Access partner website records.

4. Client Panel

A client-facing workspace for accessing services, orders, invoices, meetings, and support.

Key Features

Client Dashboard — View order summaries, active orders, pending actions, and payment information.

Order Management — View order listings and individual order details.

Document Uploads — Upload documents associated with orders and view available order documents.

Certificates — Download available service certificates.

Invoice Management — View invoices, preview invoice details, and download invoices.

Meeting Management — View and schedule meetings, update meeting details, submit availability, and request rescheduling.

Support Tickets — Create tickets, reply to conversations, confirm resolution, and reopen tickets when necessary.

Technology Stack

Backend: PHP, Laravel

Frontend: Blade templates, HTML, CSS, JavaScript

Asynchronous Interactions: AJAX-based endpoints

Database: Application-configured database

Architecture: Laravel MVC, route-based controllers, and middleware-protected application areas

Technical Highlights

Dedicated route groups for Admin, Marketing, Back Office, and Client workflows.

Authentication and role-based access restrictions on protected application areas.

CRUD-style operations for client records, services, documents, and operational data.

Payment tracking, invoice handling, and follow-up workflows.

AJAX endpoints for interactive data loading and updates.

PDF export and download functionality for supported financial documents and reports.

Financial reconciliation and computation workflows with draft, save, and finalization operations.

Application Screenshots

Add sanitized screenshots of the following panels to the repository:

Panel

Screenshot

Admin

docs/screenshots/admin-dashboard.png

Service Selling Analysis

docs/screenshots/service-selling-analysis.png

Payment Management

docs/screenshots/payment-management.png

Marketing

docs/screenshots/marketing-dashboard.png

Back Office

docs/screenshots/backoffice-dashboard.png

Client

docs/screenshots/client-dashboard.png

Developer Contribution

Contributed to the development and maintenance of a live business application, working on service management workflows, client and order operations, payment tracking, document handling, role-based application areas, back-office computations, and issue resolution.

Local Development Setup

Clone the repository.

Install PHP dependencies:

composer install

Create a local environment file from .env.example and configure the required environment variables.

Generate the application key:

php artisan key:generate

Configure a local database and run the appropriate migrations only after reviewing the migration files.

Install frontend dependencies and build assets if required by the project:

npm install
npm run build

Start the local development server:

php artisan serve

Refer to the project's dependency files and configuration for the exact PHP, database, and Node.js requirements.

Security Notice

This repository is intended for code review and professional demonstration. Never commit environment secrets, database credentials, API keys, private documents, or identifiable customer information. Production screenshots should be sanitized before public sharing.
