# Changelog

## Enhanced knowledge build
- Expanded the local Markdown knowledge corpus from four starter files to fifteen reference files.
- Added general reference material for software engineering, web/frontend development, databases/SQL, API engineering, cybersecurity, networking/cloud, AI/RAG, study methods and Laravel/React full-stack development.
- Added a repository-derived LWF architecture document describing verified models, services and the current lesson/material retrieval limitation.
- Improved local retrieval with BM25-style ranking, heading/title weighting, technical synonym expansion, phrase matching and query-coverage scoring.
- Added configurable retrieval depth, prompt evidence budget, conversation history window and aggregate text-attachment context budget.
- Strengthened assistant instructions for teaching, source attribution, uncertainty, conversation continuity, academic integrity and secure engineering.
- Updated the interface copy to disclose the expanded knowledge library.

## Important limitation
This package still uses lexical Markdown retrieval, not vector embeddings. The LWF-specific documentation is not a complete product manual. General reference material supports education and engineering explanations but must not be presented as verified LWF policy or course-specific content.
