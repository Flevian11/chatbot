# LWF AI Assistant — OpenRouter + Knowledge Base

A persistent, multi-model PHP and JavaScript chatbot for `https://lwf.yaliid.cloud/class-chatbot`. It runs independently of the Laravel LMS but is branded for Learn With Flevian.

## Features

- Modern responsive LWF-branded chat interface.
- Two operating modes: **LWF LMS focused** and **General purpose**.
- OpenRouter Chat Completions API integration from PHP.
- Server-side API key in `.env`; the browser never receives the key.
- Persistent conversations stored server-side, with automatic titles derived from the first user message.
- Conversation history sidebar; chats are bound to the browser's PHP session.
- Up to 10 retained attachments per chat, 5 MB per file (25 MB total per chat), with a configurable aggregate text-context budget.
- Expanded local knowledge library covering LWF platform context, learning design, software engineering, web development, Laravel/React, databases, API engineering, cybersecurity, networking/cloud deployment and AI/RAG.\n- Improved BM25-style retrieval with heading/title weighting, technical synonym expansion, phrase matching, query coverage scoring and bounded evidence context.
- PDF and image attachments sent to OpenRouter; text, Markdown, CSV, JSON, XML and log files are read server-side and included as untrusted context.
- Markdown-formatted assistant responses with sanitised HTML, tables, links and syntax-highlighted code blocks.
- Source filename disclosure when retrieved knowledge is used.
- Input validation, CSRF protection, session cookie hardening and basic per-session/IP rate limiting.
- Bounded request timeout and safe public error messages.
- Server-side application logs and token usage metadata when returned by the provider.
- No database or Composer dependencies required for the starter build.

## Requirements

- PHP 8.1+ recommended.
- PHP extensions: cURL, mbstring, fileinfo, JSON and OpenSSL.
- Apache or another PHP-capable web server.
- Outbound HTTPS connectivity to `openrouter.ai`.
- An OpenRouter account and API key with available credits / an appropriate key limit.

## Deployment

See `DEPLOYMENT.md` for the complete deployment and hardening checklist.

Quick start:
1. Upload the project folder to a location served by PHP, for example `public/chatbot/`.
2. Copy `.env.example` to `.env`.
3. Set `OPENROUTER_API_KEY` to the key created in your OpenRouter dashboard.
4. Review the three IDs in `OPENROUTER_MODELS` against the live OpenRouter catalogue. The API uses OpenRouter's ordered `models` fallback array.
5. Confirm the `storage/` directory is writable by the PHP process but not web-accessible.
6. Open `https://lwf.yaliid.cloud/class-chatbot/`.

## Knowledge base

Add or edit `.md` files in `knowledge/`. The retrieval layer reads Markdown files at request time and ranks coherent sections using an improved BM25-style lexical score. It weights title and heading matches, expands common technical synonyms, rewards exact phrase matches and query coverage, and bounds the context sent to the model. Defaults are eight passages in LMS mode and five in General mode; tune them with RETRIEVAL_LIMIT_LMS, RETRIEVAL_LIMIT_GENERAL and MAX_KNOWLEDGE_CHARS.

For each knowledge file:
- Use descriptive headings (`#`, `##`, `###`).
- Keep each section focused on one workflow or topic.
- Include the exact, approved instructions and terminology.
- Add document owner, review date and product version in the document.
- Remove outdated or contradictory instructions.

The knowledge directory now contains a broader educational and engineering reference pack alongside a small set of LWF-specific starter documents. The general reference material can support teaching and technical explanations; it must not be treated as proof of LWF-specific product behaviour. The LWF-specific files remain starter documentation, not a complete or authoritative LWF manual. Replace or extend them with current approved product documentation before exposing the chatbot to real learners.

### Retrieval limitations

This release uses improved lexical retrieval, not embeddings or a vector database. It remains dependency-free and is suitable for a curated classroom knowledge pack. Synonym expansion improves common paraphrases, but lexical retrieval can still miss semantic matches. For a larger production corpus, replace `lib/knowledge.php` with an embedding-based retrieval adapter (vector database or hosted vector search), add document ingestion and access-control filters, and evaluate retrieval quality against a curated test set.

## Operating modes

- **LWF LMS mode:** prioritises retrieved LWF documentation. If evidence is missing, the assistant should say so rather than invent an LWF procedure.
- **General mode:** answers broader questions but must distinguish general advice from verified LWF-specific information.

Both modes use the same API endpoint and conversation session. The mode is selected by the frontend and validated server-side.

## Conversation behaviour

The backend persists conversations as session-owned JSON records and includes the latest configurable message window (default 80 messages) in each request. This preserves conversational continuity across reloads for the same browser session, but is not a durable user-account memory system. It is not a user-account memory system.

## Security notes

- Never commit `.env` or paste API keys into the frontend.
- Configure HTTPS in production.
- Keep `storage/` outside the public document root where possible. If it must remain inside, retain the deny rules and verify them on the actual host.
- Set restrictive filesystem permissions.
- Add authentication and role-based authorisation before using the assistant for internal or student-specific content.
- Do not add private learner records to the public knowledge directory.
- The starter does not implement user authentication, private-document ACLs, tool execution, account actions, or a support-ticket integration.
- Rate limiting is basic file-based protection and may not coordinate across multiple servers. Use a shared cache/rate-limit service for multi-instance production.
- Review OpenRouter and selected provider privacy, retention and routing terms before sending personal or confidential information.

## Troubleshooting

- `The OpenRouter API key is not configured`: create `.env` and set the key.
- `OpenRouter authentication failed`: verify the key is active and copied correctly.
- Insufficient credits/spending limit: review OpenRouter credits and key limits.
- `cURL` errors: enable PHP cURL, check DNS, TLS certificate trust and outbound HTTPS firewall rules.
- `mb_*` function missing: enable PHP mbstring.
- Permission errors: make `storage/` writable by PHP while keeping it inaccessible over HTTP.
- Model unavailable: choose a current model ID from the OpenRouter model catalogue.

## Classroom safety

Use this project with non-sensitive test questions first. Do not use it to make decisions about grades, payments, admissions, safeguarding or learner eligibility. Human review is required for consequential decisions.


## OpenRouter model failover

The configured three-model chain is sent using OpenRouter's `models` parameter. It is an ordered failover chain, not three parallel simultaneous generations. OpenRouter attempts the next model when the earlier model/provider fails under its fallback rules; the actual serving model is returned in the response and displayed in the conversation. Billing follows the model that served the response. Review the current model catalogue and pricing before production use.

## Response formatting and attachments

Assistant output is rendered as Markdown with sanitisation before display. Headings, lists, tables, links, blockquotes and fenced code blocks are supported. Code blocks receive syntax highlighting where the browser can load the highlighting library.

Each uploaded file may be up to 5 MB. Supported files are PDF, common web images, TXT, Markdown, CSV, JSON, XML and LOG. PDF and image files are passed as multimodal content to OpenRouter; text-like files are read by PHP and supplied as reference context. Attachments remain linked to their conversation and are re-sent with later turns, so repeatedly using large PDFs/images can increase token use and request size. The conversation can retain up to 10 attachments / 25 MB total.

## Expanded knowledge pack

The included educational reference files cover:
- Computing and software engineering foundations.
- Web development, frontend engineering and React.
- Relational databases, SQL, transactions and query performance.
- API design, HTTP, Postman, webhooks and payment integration safety.
- Defensive cybersecurity and application security.
- Networking, cloud hosting, deployment and observability.
- Artificial intelligence, language models, RAG and knowledge evaluation.
- Learning methods, academic skills and integrity.
- Laravel, Eloquent, Inertia, React and TypeScript.

These are general educational references. They do not replace official course notes, institution policies, or verified LWF operational documentation. Add approved course-specific Markdown files to `knowledge/` to ground the assistant in the actual curriculum.

The repository-derived LWF architecture reference is based on the inspected `Flevian11/lwf-lms` main branch. It explicitly documents the distinction between student-progress context and actual lesson/material retrieval.
