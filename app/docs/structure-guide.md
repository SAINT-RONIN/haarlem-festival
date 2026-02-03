# Project Structure Guide

This guide explains what each folder is for. The goal is to keep a clear MVC structure and keep files in the right place.

## Root

### `.idea/`

* IDE settings.
* No project code.

## `app/`

### `docs/`

* Project documentation only.
* Examples: setup notes, database notes, sprint notes, structure guides.

### `public/`

* Public web root. The browser can access files here.
* Only put public files here.
* Do not put secrets or backend code here.

#### `public/assets/`

Static files used by the website.

* `css/`: CSS files.
* `js/`: JavaScript files. Keep JS small, only for minor dynamic parts.

## `app/src/`

Backend PHP code.

### `Controllers/`

* Handles requests.
* Reads input (GET, POST).
* Calls services or repositories.
* Chooses a view and passes data.
* No SQL queries here.
* No complex business rules here.

### `Models/`

* Data objects that represent your domain.
* Examples: User, Page, Event, Order.
* No SQL here.
* No HTML rendering here.

### `Repositories/`

* Database access using PDO and prepared statements.
* Contains SQL queries.
* Returns models or arrays.
* No HTML output here.

#### `Repositories/Interfaces/`

* Interfaces for repositories.
* Defines which methods a repository must have.

### `Services/`

* Business logic and workflows.
* This is where rules belong, for example: registration rules, login checks, checkout rules, availability checks.
* Services can call repositories.
* Services should not contain SQL.

**Password hashing:**

* The decision *when* to hash (for example during register, or when changing password) belongs in the **service**.
* The actual hashing can be done by PHP’s built-in `password_hash()` directly inside the service, or via a small utility class.

#### `Services/Interfaces/`

* Interfaces for services.

* Keeps controllers consistent.

* Interfaces for services.

* Keeps controllers consistent.

### `Views/`

* PHP templates.
* Displays data passed from controllers.
* Minimal logic only (loops and simple if statements).
* Never call repositories or services from a view.

#### `Views/pages/`

* Full page templates.
* Examples: home, about, contact, event detail.

#### `Views/partials/`

* Reusable view parts.
* Examples: header, footer, navigation.

### `ViewModels/`

* Data containers made for specific views.
* Use when a view needs combined data.
* Example: CMS edit page needs page info, sections, and validation errors.

### `Helpers/`

* Small helper classes or functions used in many places.
* Examples: session helper, URL helper, validation helper.

### `Infrastructure/`

* Technical foundation code.

* Examples: database connection setup, router, base controller.

* Technical foundation code.

* Examples: database connection setup, router, base controller.

### `Utils/`

* Small technical utilities that are not business rules.
* Examples: file upload validation, image validation.

**Password hashing utility (optional):**

* If we want reuse and clean code(Of course we want this), we should add a `PasswordHasher` utility here.
* The service still decides when to hash, the utility only performs the hash/verify.

### `Exceptions/`

* Custom exceptions.

* Examples: NotFoundException, ValidationException.

* Custom exceptions.

* Examples: NotFoundException, ValidationException.

## Enums

Enums are used to represent a fixed set of predefined values in a clear and safe way.

They are used when a value:

* Has a limited number of valid options
* Should not be freely typed as a string
* Is shared across multiple parts of the application

### Why we use Enums

* Prevents magic strings like "admin" or "employee" scattered through the code
* Reduces bugs caused by typos or inconsistent values
* Makes the code easier to read and understand
* Centralises important domain values in one place

### Examples of Enum usage

* User roles: Visitor, Customer, Employee, Administrator
* Order status: Pending, Paid, Failed, Expired
* Ticket status: New, Scanned

Enums are stored in:

* `app/src/Enums/`

They can be used by:

* Models (to store or expose state)
* Services (to apply business rules)
* ViewModels (to display readable values)

Enums do not:

* Contain business logic
* Access the database
* Render views

## `Enums/`

* Enums represent a fixed set of allowed values.
* Use them to avoid magic strings and prevent typos.

**Examples:**

* `UserRole`: Customer, Employee, Administrator (Visitor is not stored because it is not logged in)
* `OrderStatus`: Pending, Paid, Failed, Expired
* `TicketStatus`: New, Scanned

**Used by:** Models, Services, ViewModels.

## Project rules

### MVC responsibility

* Controller: request handling and navigation.
* Service: business rules.
* Repository: database queries.
* View: display only.

### Limited JavaScript

* Use JS only for small UI improvements.
* Do not build complex page state with JS.
* Do not use frameworks.
