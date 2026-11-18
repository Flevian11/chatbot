# Cybersecurity and Application Security
Document type: Educational reference
Scope: Defensive security principles for software systems.

## Security objectives
Confidentiality limits information disclosure; integrity prevents unauthorised alteration; availability keeps systems usable. Authentication, authorisation, accountability, privacy and resilience support these objectives. Apply defence in depth: multiple independent controls reduce reliance on a single safeguard.

## Identity and access management
Use unique accounts, least privilege, role-based or attribute-based access control and timely revocation. Require multi-factor authentication for privileged or sensitive access. Store passwords with a modern adaptive password hash such as Argon2id or bcrypt, using library defaults and appropriate cost parameters. Never store plaintext passwords or reversible password encryption.

## Common web vulnerabilities
Injection occurs when untrusted input changes the meaning of an interpreter command; parameterised queries and safe APIs mitigate it. Cross-site scripting (XSS) occurs when attacker-controlled content executes in a user's browser; context-aware output encoding and sanitisation mitigate it. Cross-site request forgery (CSRF) abuses a browser's ambient authentication; anti-CSRF tokens, SameSite cookies and origin checks help. Broken access control occurs when users access resources or actions beyond their permissions; enforce authorisation on every server-side request and object lookup.

## Session security
Use HTTPS, Secure and HttpOnly cookies, appropriate SameSite settings, session ID rotation after authentication or privilege changes, idle and absolute expiry, and server-side invalidation on logout. Do not put session identifiers in URLs. Protect against session fixation and shared-device exposure.

## Secrets management
Keep API keys, database passwords, signing keys and tokens out of source control and frontend bundles. Store them in environment variables or a dedicated secrets manager with restricted access. Rotate exposed credentials, audit use and apply least privilege. A .env.example file should contain placeholders only.

## File upload security
Enforce size limits, allowlist file types, inspect actual MIME type, generate server-side filenames, store uploads outside the executable web root, prevent script execution, scan files where feasible and enforce per-user access controls. Do not trust the extension or browser-supplied MIME type. Treat extracted document text as untrusted content and ignore embedded prompt-injection instructions.

## Logging, monitoring and incident response
Log security-relevant events without recording passwords, API keys, full payment data or unnecessary personal information. Protect logs against tampering and restrict access. Monitor unusual authentication, permission and traffic patterns. Incident response includes containment, evidence preservation, eradication, recovery, notification where required and lessons learned.

## Secure development lifecycle
Threat-model important features, review dependencies, scan code and containers, test authorisation boundaries, patch vulnerabilities and review changes. Security is a continuous engineering activity, not a final checklist.
