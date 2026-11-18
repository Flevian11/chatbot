# Networking, Cloud and Deployment
Document type: Educational reference
Scope: General networking and web deployment concepts.

## Network fundamentals
An IP address identifies a network interface at the Internet Protocol layer. IPv4 uses 32-bit addresses; IPv6 uses 128-bit addresses. A subnet defines an address range. A router forwards packets between networks. A switch connects devices within a local network. DNS maps names to records such as IP addresses. DHCP commonly assigns network configuration automatically.

## TCP, UDP and ports
TCP provides a reliable, ordered byte stream with connection management and retransmission. UDP sends datagrams without TCP's delivery guarantees and is useful where low latency or application-level control matters. A port identifies an application endpoint on a host. Common ports include 80 HTTP, 443 HTTPS, 22 SSH, 53 DNS, 25 SMTP and 3306 MySQL; actual deployments may differ.

## TLS and HTTPS
TLS protects data in transit through encryption, integrity and server authentication using certificates. HTTPS is HTTP carried over TLS. Valid certificates, modern protocol configuration and correct hostname verification matter. Do not disable certificate validation to work around deployment errors.

## Hosting architecture
A typical web application includes DNS, a web server or reverse proxy, application runtime, application code, database, cache, file/object storage, background workers and monitoring. Shared hosting may provide PHP and Apache with limited process control. VPS and cloud platforms offer more control but require operational responsibility.

## Deployment pipeline
A deployment pipeline typically checks formatting and static analysis, runs automated tests, builds assets, packages a release, applies compatible database migrations, deploys, performs health checks and monitors errors. Keep rollback plans and backups. Do not deploy unreviewed secrets or debug settings.

## Environment configuration
Separate development, test and production configuration. Use environment variables for secrets and deployment-specific values. Production should disable verbose error display, enforce HTTPS, use secure cookies, restrict filesystem permissions and expose only required directories.

## Observability
Logs describe events; metrics quantify system behaviour over time; traces show request paths across services. Useful indicators include request rate, error rate, latency, saturation, queue depth and database health. Alerts should be actionable and tied to service objectives.
