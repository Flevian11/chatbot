# Laravel, React and Full-Stack Engineering
Document type: Educational reference
Scope: General framework guidance. Exact LWF implementation details must be verified against the deployed repository and version.

## Laravel application structure
Laravel is a PHP application framework. Common responsibilities include routes, middleware, controllers, request validation, models, migrations, policies, services, events, jobs and views. Routes map requests to handlers. Middleware performs cross-cutting checks such as authentication. Controllers coordinate HTTP input and output; domain logic is often clearer in dedicated services. Form Requests can centralise validation and authorisation.

## Eloquent and database design
Eloquent models represent database records and relationships. Common relationships include hasMany, belongsTo, belongsToMany and hasOne. Eager loading with with() can reduce N+1 query problems. Use policies or gates for authorisation, transactions for multi-step writes and query scopes for reusable constraints. Validate and authorise server-side; hiding a button is not access control.

## Migrations, seeders and queues
Migrations version database schema. Seeders insert known development or reference data and should be safe to run in the intended environment. Queues move slow work such as email, document processing and notifications outside the request-response cycle. Jobs should be retry-safe and idempotent where possible.

## React and Inertia
React components describe UI. Props provide inputs; state holds changing local data. Inertia connects server-side routes and frontend page components without requiring a separate REST API for every page. Keep server-authoritative business rules on the backend. Use loading and error states for asynchronous operations and validate incoming props at clear boundaries.

## TypeScript
Use interfaces or types to describe data contracts. Avoid using any as a default escape hatch. Narrow unknown values with runtime checks. Static types do not validate JSON received from a server. Keep API response types aligned with backend validation and serialization.

## Authentication and authorisation
Authentication verifies identity; authorisation checks permissions. Protect routes with middleware and sensitive records with policies or explicit ownership checks. Ensure object-level access control on every read, update and delete. Test both permitted and denied cases.

## Production practices
Use environment-specific configuration, cache configuration/routes where appropriate, build frontend assets, run migrations carefully, configure queues and scheduled tasks, monitor logs and keep backups. Never commit .env, production credentials or user-uploaded private files.
