# API Engineering and Integration
Document type: Educational reference
Scope: General API design, testing and integration patterns.

## API purpose and contracts
An API defines how software components communicate. A good API contract documents resources, operations, request schemas, response schemas, authentication, authorisation, validation, error formats, pagination, rate limits and versioning. OpenAPI is a common machine-readable specification for HTTP APIs.

## REST design
REST-style APIs commonly model resources with nouns and use HTTP methods for operations. Use consistent resource naming, predictable status codes and representations. Avoid exposing database implementation details as public contracts. Design operations to be safe or idempotent where their semantics allow it.

## Validation and errors
Validate types, lengths, formats, required fields, allowed values and cross-field rules on the server. Return useful but non-sensitive error messages. A validation error should identify which field failed and why. Do not return SQL errors, stack traces, secrets or internal file paths to clients.

## Authentication versus authorisation
Authentication establishes who or what is making a request. Authorisation determines what that identity may do. API keys identify a client or integration but should be scoped, rotated and stored securely. OAuth 2.0 is an authorisation framework; OpenID Connect adds an identity layer. Bearer tokens must be sent over HTTPS and never placed in URLs.

## Pagination, filtering and rate limits
Large collections should use pagination. Offset pagination is simple but can become inefficient or unstable as data changes; cursor pagination is often better for large or frequently changing datasets. Define allowed filters and sort fields. Rate limits protect availability and should return 429 with suitable retry guidance when appropriate.

## Webhooks
A webhook delivers an event to a configured endpoint. Verify authenticity using a signature over the raw request body, enforce timestamp tolerance where supported, prevent replay, respond quickly and process heavy work asynchronously. Make handlers idempotent because providers may retry delivery. Record event identifiers and delivery outcomes.

## API testing with Postman
Test successful requests and negative cases. Verify status code, response body, headers, schema, authentication, validation, pagination, rate limiting and error behaviour. Keep secrets in Postman environments or secret stores, not exported collections. Test GET, POST, PUT, PATCH and DELETE according to the API contract and use sandbox environments for payment integrations.

## Payment integration safety
Use the provider's official sandbox and documentation. Verify callback signatures or credentials, validate transaction references and amounts server-side, handle duplicate callbacks idempotently and reconcile asynchronous payment status. Never treat a browser redirect alone as proof of payment. Never store card security codes or expose provider credentials.

## Versioning and compatibility
Avoid breaking clients without a migration plan. Version APIs when incompatible changes are necessary. Prefer additive changes, deprecation notices and documented sunset dates. Monitor client usage before removing old fields or endpoints.
