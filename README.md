Multi-Tenant SaaS CRM with Custom JWT Guard
This is a Laravel-based multi-tenant SaaS CRM platform. The application uses a multi-database architecture where each tenant has their own dedicated database to ensure strict data isolation. Authentication is handled by a custom JWT guard, which identifies tenants via claims in the JWT rather than subdomains.

1. Features

Multi-Tenancy: Each tenant (company) operates on a separate database. A central database manages tenant information and global admins.

Custom JWT Guard: A custom jwt-tenant guard dynamically switches the database connection based on the tenant_id claim in the JWT, authenticating the user from the correct tenant database.

Strict Isolation: User and data are strictly isolated per tenant.

API Endpoints: The project includes separate API endpoints for global admins (on the system DB) and for individual tenants (on their respective databases).

Middleware: A middleware ensures that every tenant request has a valid JWT, belongs to an active tenant, and uses the correct tenant database connection.

2. Getting Started
   Prerequisites
   PHP (version compatible with Laravel)

Composer

MySQL or a compatible database server

Installation
Clone the repository:

Bash

git clone your-repository-url.git
cd your-project-folder
Install Composer dependencies:

Bash

composer install
Create your .env file from the example and generate an application key:

Bash

cp .env.example .env
php artisan key:generate 3. Database Setup
Open your .env file and configure the connection for the central system database. This database will manage all tenants.

Ini, TOML

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_system_db
DB_USERNAME=root
DB_PASSWORD=
Create the your_system_db database in your database management system.

4. How to Run the Project
   Run Migrations: Execute the migrations for the central system database. This creates the tenants table.

Bash

php artisan migrate

Seed Tenants: Run the seeder to provision and set up two default tenants, acme and globex. This process will automatically create a new database for each tenant, run their specific migrations, and seed them with sample users and data.

Bash

php artisan db:seed --class=TenantSeeder 5. How to Use JWT Tokens
How to Generate a Token
To obtain a JWT for a tenant user, send a POST request to the /tenant/login endpoint with the user's credentials. The response will contain the JWT.

Example POST request to /tenant/login:

JSON

{
"email": "user@example.com",
"password": "password"
}
The JWT will include claims like

sub (user ID), tenant_id (tenant database ID), and role (user role).

How to Use the Token
Include the generated JWT in the Authorization header of all subsequent requests to tenant APIs.

Authorization: Bearer [your_jwt_token_here] 6. API Endpoints
A Postman collection is included in the deliverables for easy API testing.

Admin APIs (using an admin JWT and system DB)

POST /admin/tenants: Create a new tenant, including database provisioning.

GET /admin/tenants: List all tenants with their status.

PATCH /admin/tenants/{id}/suspend: Suspend a tenant, which will prevent their JWTs from working.

Tenant APIs (using a tenant JWT and tenant DB)

POST /tenant/login: Authenticate a tenant user and return a JWT.

GET /contacts: List contacts for the authenticated tenant.

POST /contacts: Create a new contact.

POST /deals: Create a new deal.

GET /reports/deals: Get a deals summary (e.g., total won revenue).

7. Deliverables
   A Laravel project on GitHub.

This

README.md file with clear setup and usage instructions.

A seeder to provision and seed at least two tenants (

acme and globex).

A Postman collection (or Swagger docs) with example API requests.

Unit tests demonstrating tenant isolation and JWT validation.

Sources
