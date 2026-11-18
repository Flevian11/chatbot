# Computing and Software Engineering Foundations
Document type: Educational reference
Scope: General computing and software engineering concepts; not an LWF policy document.

## What software engineering means
Software engineering applies systematic, measurable and disciplined methods to the specification, design, implementation, testing, deployment, operation and maintenance of software. It is broader than writing code. A software product also includes requirements, architecture, data, security, usability, documentation, monitoring and change management.

## Software development lifecycle
A practical lifecycle includes discovery and requirements, analysis, architecture and design, implementation, verification, release, operation and maintenance. These stages are iterative rather than necessarily linear. Agile teams deliver small increments, inspect feedback and adapt priorities. A useful requirement is specific, testable, traceable and tied to a user or business outcome.

## Functional and non-functional requirements
Functional requirements describe behaviours or capabilities, such as creating an account or submitting an assignment. Non-functional requirements describe quality attributes and constraints: performance, availability, security, accessibility, maintainability, scalability, compatibility and usability. Acceptance criteria make expected behaviour observable and testable.

## Architecture and design principles
Separation of concerns assigns distinct responsibilities to components. High cohesion means a module's responsibilities belong together; low coupling reduces unnecessary dependencies between modules. Encapsulation protects internal state behind clear interfaces. Abstraction exposes essential behaviour while hiding implementation details. SOLID is a set of object-oriented design principles: single responsibility, open/closed, Liskov substitution, interface segregation and dependency inversion. Apply principles pragmatically; avoid unnecessary abstraction.

## Testing strategy
Unit tests isolate small units such as functions or classes. Integration tests verify collaboration between components such as controllers, services and databases. End-to-end tests exercise complete user workflows. Contract tests verify API expectations. Regression tests protect previously working behaviour. A good test asserts observable outcomes, includes edge cases and is deterministic.

## Debugging method
Reproduce the failure; record exact inputs and environment; read the complete error and stack trace; identify the smallest failing boundary; inspect assumptions and state; form one hypothesis at a time; change one variable; add a regression test; verify the fix and document the cause. Avoid random edits that obscure the root cause.

## Version control and collaboration
Git records snapshots of project history. A branch isolates a line of work; a commit should represent a coherent change; a pull request supports review and discussion. Keep secrets out of repositories, use meaningful commit messages, review diffs and resolve conflicts deliberately. A .gitignore file prevents selected local/generated files from being tracked, but does not remove files already committed.

## Maintainability and technical debt
Readable naming, small cohesive modules, automated tests, dependency management, clear interfaces and current documentation reduce maintenance cost. Technical debt is a deliberate or accidental shortcut that creates future cost; track it and prioritise by risk rather than treating all debt equally.
