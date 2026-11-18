# Web Development and Frontend Engineering
Document type: Educational reference
Scope: General web development guidance.

## How the web works
A browser resolves a domain through DNS, establishes a network connection, negotiates TLS for HTTPS, sends an HTTP request and processes the server response. HTML defines document structure, CSS controls presentation and layout, and JavaScript provides behaviour. The browser may also load images, fonts, stylesheets and API responses.

## HTTP fundamentals
HTTP is a request-response protocol. A request contains a method, target URL, headers and optionally a body. A response contains a status code, headers and optionally a body. GET retrieves a representation; POST submits data or triggers processing; PUT replaces a resource representation; PATCH applies a partial modification; DELETE requests removal. Safe methods should not intentionally change server state. Idempotent operations have the same intended effect when repeated.

Common status codes: 200 success; 201 created; 202 accepted for processing; 204 success with no response body; 400 malformed or invalid request; 401 unauthenticated; 403 authenticated but forbidden; 404 not found; 409 conflict; 422 validation failure; 429 rate limited; 500 server error; 502/503/504 gateway or availability failures.

## HTML, CSS and responsive design
Use semantic HTML elements that communicate structure and purpose. Labels should be associated with form controls. Keyboard navigation, visible focus, sufficient contrast, meaningful alternative text and clear error messages improve accessibility. Responsive layouts adapt to viewport size using flexible grids, fluid sizing and media queries rather than fixed desktop dimensions.

## JavaScript and TypeScript
JavaScript executes in browsers and server runtimes. TypeScript adds static type checking and compiles to JavaScript. Types document contracts and catch many mistakes before runtime, but do not validate untrusted network input automatically. Validate data at runtime at API boundaries. Prefer small functions, explicit state transitions, predictable error handling and safe DOM updates.

## React concepts
React builds interfaces from components. Props pass data into components; state represents data that changes over time; effects synchronise with external systems. Keep state minimal and derive values where possible. Use stable keys for rendered collections. Avoid mutating state directly. Handle loading, empty, error and success states. Do not put secrets in frontend environment variables because bundled client code is visible to users.

## Frontend security
Escape or sanitise untrusted HTML before rendering. Prefer textContent for plain text. Validate inputs on the server even when client-side validation exists. Protect authenticated requests against CSRF where cookie authentication is used. Avoid exposing tokens, API keys, stack traces or private records in browser code and logs.

## Performance
Reduce unnecessary network requests and large assets. Use code splitting, caching, image optimisation, lazy loading and efficient rendering when appropriate. Measure performance before optimising. Core Web Vitals describe loading, interactivity and visual stability from the user's perspective.
