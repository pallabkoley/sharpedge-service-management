<h1 align="center">SharpEdge – CA Firm Management System</h1>

<p align="center">
A <strong>Laravel-based ERP</strong> for a <strong>Chartered Accountancy (CA) firm</strong> — managing clients, service sales, orders, invoices, payments, documents, staff tasks, and accounting operations through <strong>4 role-based panels</strong>.
</p>

<hr>

<h2>🎯 Overview</h2>

<p>
SharpEdge digitizes the complete operational lifecycle of a CA firm — from <strong>service sale → order → document verification → task processing → financial computation → client delivery</strong> — replacing manual coordination across teams with a single traceable workflow.
</p>

<p>
Built with strict <strong>role-based access control (RBAC)</strong> so each panel (Admin, Marketing, Backoffice, Client) sees only what it needs.
</p>

<hr>

<h2>🧩 Panels &amp; Key Modules</h2>

<h3>1️⃣ Admin Panel — Control Tower</h3>
<ul>
    <li><strong>Client Master</strong> — client profiles, cases, compliances, turnover, bank accounts, portal credentials</li>
    <li><strong>Service &amp; Order Management</strong> — service catalog, work-process steps, order creation &amp; approval</li>
    <li><strong>Invoices &amp; Payments</strong> — invoice generation/revision, payment proof approval, receipts, pay-later tracking</li>
    <li><strong>Document Verification</strong> — approve/reject marketing-uploaded documents, bulk actions</li>
    <li><strong>Task Assignment &amp; Verification</strong> — assign/reassign tasks to Backoffice, verify submitted work</li>
    <li><strong>Staff Management</strong> — staff onboarding, status control</li>
    <li><strong>Coupons &amp; UPI/QR Settings</strong> — discount control, QR-based payment collection</li>
    <li><strong>HR Suite</strong> — attendance, leave approvals &amp; policies, holidays, notices, reports</li>
    <li><strong>Analytics</strong> — service selling analysis, workflow health dashboard</li>
    <li><strong>Support &amp; Meetings</strong> — ticket resolution, meeting approval</li>
</ul>

<h3>2️⃣ Marketing Panel — Sales Front</h3>
<ul>
    <li><strong>Client &amp; Order Creation</strong> — quick order creation, coupon validation, amount calculation, invoice generation</li>
    <li><strong>Document Upload</strong> — client &amp; order documents (AJAX modal-based)</li>
    <li><strong>Payment Follow-ups</strong> — track outstanding dues with full follow-up history</li>
    <li><strong>Client Coordination</strong> — meetings, support tickets, activity monitoring</li>
    <li><strong>HR</strong> — attendance clock-in/out, leave requests, holiday calendar</li>
    <li><strong>Dashboard</strong> — chart-based sales/order performance tracking</li>
</ul>

<h3>3️⃣ Back Office Panel — Finance Engine</h3>
<ul>
    <li><strong>Computations Hub</strong> — centralized financial computation sheets</li>
    <li><strong>Financial Statements</strong> — P&amp;L, Balance Sheet, Trading Account, Appropriation, with <strong>Draft-to-Finalize lock</strong></li>
    <li><strong>6 Reconciliation Modules</strong> — Debtor, Creditor, Bank, Sales, Purchase, Assets</li>
    <li><strong>Debit / Credit Note Registers</strong> — breakups and finalization</li>
    <li><strong>Task Workflow</strong> — start → wait → submit → renew cycle with remarks and document handling</li>
    <li><strong>Client Documents &amp; Reports</strong> — global document access, P&amp;L/BS report export</li>
    <li><strong>HR &amp; Support</strong> — attendance, leave, tickets</li>
</ul>

<h3>4️⃣ Client Panel — Self-Service Portal</h3>
<ul>
    <li><strong>Order Tracking</strong> — view purchased services, order status, and download certificates</li>
    <li><strong>Invoices</strong> — preview and download invoices</li>
    <li><strong>Document Submission</strong> — upload required documents per order</li>
    <li><strong>Support Tickets</strong> — create, reply, confirm resolution, reopen</li>
    <li><strong>Meetings</strong> — book, reschedule, submit availability</li>
</ul>

<hr>

<h2>🔄 Business Flow</h2>

<pre>
Marketing          →  Admin              →  Backoffice           →  Client
─────────────────────────────────────────────────────────────────────────────
Client coord.        Document verify       Task processing         Order view
Order create         Order/payment approve Financial compute     Invoice download
Doc upload           Invoice generate      P&amp;L / Balance Sheet     Document upload
Payment follow-up    Task assign           Reconciliation         Support ticket
                                           Report finalize         Meeting booking
</pre>

<p>
Each stage is <strong>role-gated</strong>, creating a transparent, auditable workflow from sales to financial delivery.
</p>

<hr>

<h2>💡 Business Impact</h2>
<ul>
    <li><strong>Centralized Operations</strong> — clients, services, orders, payments, documents, and accounting in one platform</li>
    <li><strong>Workflow Automation</strong> — task assignment, verification, order tracking, and payment follow-ups without manual coordination</li>
    <li><strong>Financial Integrity</strong> — Draft-to-Finalize lock and transaction-safe computations prevent finalized data corruption</li>
    <li><strong>Revenue Visibility</strong> — end-to-end cycle from order → payment → invoice → receipt → follow-up</li>
    <li><strong>Client Transparency</strong> — self-service portal reduces manual back-and-forth</li>
    <li><strong>Scalable HR</strong> — attendance, leave, and notices integrated across all internal panels</li>
</ul>

<hr>

<h2>🚀 Key Highlights</h2>
<ul>
    <li><strong>4-role RBAC architecture</strong> (Admin, Marketing, Backoffice, Client)</li>
    <li><strong>Draft-to-Finalize lock</strong> on all financial statements to protect finalized records</li>
    <li><strong>6 reconciliation modules</strong> covering audit-critical financial checks</li>
    <li><strong>AJAX-driven admin panels</strong> for smooth, reload-free operations on heavy data tables</li>
    <li><strong>QR/UPI payment integration</strong> for online collection</li>
    <li><strong>Complete revenue cycle</strong> — order → payment → invoice → receipt → follow-up</li>
</ul>

<hr>

<h2>🖼️ System Preview</h2>

<table>
    <tr>
        <td align="center" width="50%">
            <strong>Admin Dashboard</strong><br><br>
            <img src="screenshots/Admin%20panel%20Dashboard.png" alt="Admin Dashboard" width="350">
        </td>
        <td align="center" width="50%">
            <strong>Admin Service Selling</strong><br><br>
            <img src="screenshots/Admin%20panel%20service%20selling.png" alt="Admin Service Selling" width="350">
        </td>
    </tr>
    <tr>
        <td align="center" width="50%">
            <strong>Admin All Orders</strong><br><br>
            <img src="screenshots/Admin%20Panel%20All%20orders.png" alt="Admin All Orders" width="350">
        </td>
        <td align="center" width="50%">
            <strong>Marketing Dashboard</strong><br><br>
            <img src="screenshots/MaketingpanelDashbaord.png" alt="Marketing Dashboard" width="350">
        </td>
    </tr>
    <tr>
        <td align="center" width="50%">
            <strong>Back Office Dashboard</strong><br><br>
            <img src="screenshots/Backoffice%20Dashboard.png" alt="Back Office Dashboard" width="350">
        </td>
        <td align="center" width="50%">
            <strong>Client Dashboard</strong><br><br>
            <img src="screenshots/Client%20Dashboard.png" alt="Client Dashboard" width="350">
        </td>
    </tr>
</table>

<hr>

<h2>🛠️ Tech Stack</h2>

<p>
<strong>PHP</strong> · <strong>Laravel</strong> · <strong>MySQL</strong> · <strong>Blade</strong> · <strong>JavaScript</strong> · <strong>AJAX</strong> · <strong>jQuery</strong> · <strong>HTML</strong> · <strong>CSS</strong>
</p>

<hr>

<h2>👨‍💻 Contributions</h2>
<ul>
    <li>Architected and developed <strong>role-based workflows</strong> across all four panels.</li>
    <li>Implemented <strong>CRUD, service-order management, and AJAX-driven UI interactions</strong>.</li>
    <li>Built <strong>invoice generation, payment tracking, and document management</strong> modules.</li>
    <li>Contributed to <strong>financial computations, reconciliation, task workflows, and reporting</strong>.</li>
    <li>Integrated <strong>QR/UPI payment support</strong> and <strong>Draft-to-Finalize lock</strong> mechanisms.</li>
</ul>

<hr>



