# Databases and SQL
Document type: Educational reference
Scope: Relational database concepts and practical SQL.

## Relational database model
A relational database stores data in tables. Rows represent records and columns represent attributes. A primary key uniquely identifies a row. A foreign key references a key in another table and enforces referential integrity. Constraints enforce rules such as NOT NULL, UNIQUE and CHECK. Normalisation organises data to reduce duplication and update anomalies; denormalisation may be used deliberately for measured performance needs.

## SQL query structure
SELECT chooses columns or expressions; FROM identifies source tables; JOIN combines related rows; WHERE filters rows; GROUP BY forms groups; HAVING filters groups; ORDER BY sorts; LIMIT or an equivalent clause restricts returned rows. SQL logical processing order differs from the written order, so aliases generally cannot be used in WHERE.

## Joins
INNER JOIN returns matching rows. LEFT JOIN returns every left-side row and matching right-side rows, using NULL where no match exists. RIGHT JOIN is the reverse; FULL OUTER JOIN returns unmatched rows from both sides where supported. A join condition should use the intended relationship, usually primary key to foreign key. Missing or overly broad join conditions can multiply rows.

## Aggregation
COUNT counts rows or non-NULL values depending on the expression; SUM totals numeric values; AVG calculates a mean; MIN and MAX find extrema. GROUP BY defines grouping columns. WHERE filters rows before grouping; HAVING filters grouped results. Be careful when selecting non-aggregated columns that are not part of the grouping key.

## Transactions and ACID
A transaction groups operations into one logical unit. ACID means atomicity, consistency, isolation and durability. COMMIT makes a transaction permanent; ROLLBACK undoes uncommitted changes. Isolation levels balance concurrency and anomalies. Use transactions when multiple writes must succeed or fail together.

## Indexes and query performance
An index helps the database locate rows without scanning the entire table, but consumes storage and adds write overhead. Index columns frequently used in selective filters, joins and ordering after inspecting actual query plans. Avoid indexing every column. Use EXPLAIN or the database's query-plan tool to inspect scans, joins and estimated cost.

## SQL injection prevention
Never concatenate untrusted user input into SQL syntax. Use parameterised queries or prepared statements. Bind values separately from SQL structure. For dynamic column names or sort directions, use strict allowlists because identifiers usually cannot be bound as values. Apply least-privilege database accounts.

## Migrations and schema evolution
A migration is a version-controlled change to database schema. Make migrations repeatable and review their impact on existing data. Back up production data before risky changes. For zero-downtime deployments, prefer expand-and-contract changes: add compatible structures first, deploy code that supports both, migrate data, then remove obsolete structures in a later release.
