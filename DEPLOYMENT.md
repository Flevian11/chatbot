# Deployment & Production Hardening

## 1. Upload

Upload the project to a PHP-served directory, for example:

`public/chatbot/`

If your hosting account uses a document root such as `lwf/public`, the final URL might be:

`https://lwf.yaliid.cloud/class-chatbot`

Do not upload the project outside the PHP site's public tree if you expect to access it through that URL. If you can configure a separate virtual host with a public document root, prefer keeping `lib/`, `knowledge/`, `.env` and `storage/` outside the web root and expose only the front controller and static assets.

## 2. Configure OpenRouter

1. Create an OpenRouter account.
2. Create an API key in the account dashboard.
3. Apply a key spending limit suitable for the classroom.
4. Copy `.env.example` to `.env`.
5. Set `OPENROUTER_API_KEY`.
6. Choose a current model ID from https://openrouter.ai/models and set `OPENROUTER_MODELS` with three model IDs in priority order.
7. Set `APP_URL=https://lwf.yaliid.cloud/chatbot`.

Do not assume a model's price or availability is permanent. Review current model details before class and before production deployment.

## 3. PHP requirements

Confirm:
- PHP 8.1 or later.
- cURL enabled.
- mbstring enabled.
- JSON and OpenSSL enabled.
- PHP can make outbound HTTPS requests to `openrouter.ai`.

## 4. Permissions

The PHP process needs write access to `storage/` for logs and rate-limit state. Example permissions vary by host; use the narrowest ownership/permissions your host supports. Do not set broad world-writable permissions as a shortcut.

## 5. HTTPS and access

- Force HTTPS at the hosting/web-server level.
- Verify `.env`, `README.md`, `DEPLOYMENT.md`, `storage/` and `knowledge/` cannot be downloaded directly.
- Test a direct URL to a sample `.md` file and a storage path; both should be denied.
- Consider placing secrets and writable storage outside the document root.

## 6. Smoke test

1. Open the chatbot URL.
2. Send a general question.
3. Switch to LWF LMS mode and ask a question related to one of the included starter knowledge files.
4. Confirm the answer shows the knowledge source filename when relevant.
5. Ask for an undocumented, exact LWF workflow. The assistant should disclose that the starter knowledge does not confirm it.
6. Create a second conversation, reload the page, and verify the title and messages persist. Click Clear chat and confirm the current conversation is deleted.
7. Check `storage/logs/app.log` for safe operational metadata.

## 7. Before real learners use it

This classroom starter is not a complete production LMS integration. Before public use:
- Replace starter knowledge with reviewed LWF documentation.
- Add user authentication and authorisation if access should be restricted.
- Add document-level access control before retrieving non-public content.
- Define retention and deletion policies for logs and session data.
- Add shared rate limiting and abuse protection.
- Add monitoring, alerting, backups and operational ownership.
- Create an evaluation dataset of real learner questions and expected evidence.
- Test prompt injection, unsupported questions, conflicting documents and out-of-date content.
- Review OpenRouter and provider data-handling terms.
- Add human escalation routes and verified support contacts.
- Consider vector embeddings and reranking when the knowledge base grows.

## 8. Session and chat retention

Chat records are stored under `storage/chats/` and associated with a 30-day browser session cookie. The same browser profile can return to its conversations while that cookie remains valid. This is not user authentication: users on a shared browser profile may see that browser's chat history. Add authenticated account ownership before multi-user production use. The server retains the most recent 160 messages per conversation; each model request includes the latest 80 messages, subject to model context limits and provider constraints.

## 9. Remove classroom deployment

After the lesson, revoke the classroom API key if it is no longer needed, remove `.env`, and delete the temporary deployment directory. If retaining the application, rotate credentials and review access/log retention.

## Model fallback and cost note

The project sends `OPENROUTER_MODELS` as an ordered model-fallback list. OpenRouter attempts models in order when the previous model/provider fails; it does not run all three models in parallel. The returned `model` field is recorded and shown in the chat UI. Billing follows the model that actually serves the request. Verify model support and current pricing in the OpenRouter catalogue before changing this list.

## Attachments

Each attachment is limited to 5 MB. Up to five files can be selected in one upload, with a maximum of 10 retained files and 25 MB total per conversation. Supported formats are PDF, PNG, JPG/JPEG, WEBP, TXT, Markdown, CSV, JSON, XML and LOG. PDFs use OpenRouter's file content part and the Cloudflare AI PDF parser plugin; images use image input. Text-based files are read by PHP and included as reference context. Uploaded files are stored server-side under `storage/uploads/` and resent as context on subsequent messages in that conversation, which can increase token use and request size.

The PDF parser plugin may have provider-specific processing constraints or costs; review OpenRouter's current PDF documentation and usage before public deployment.

## Knowledge retrieval configuration

The expanded build uses local Markdown retrieval. Optional `.env` settings:
- `RETRIEVAL_LIMIT_LMS=8`: maximum retrieved passages in LWF mode (3–12).
- `RETRIEVAL_LIMIT_GENERAL=5`: maximum passages in General mode (2–8).
- `MAX_KNOWLEDGE_CHARS=18000`: maximum local knowledge characters placed in the prompt (4,000–30,000).
- `CHAT_HISTORY_MESSAGES=80`: recent messages included for conversation continuity (12–100).\n- `MAX_ATTACHMENT_CONTEXT_CHARS=60000`: aggregate extracted text attachment budget per request (10,000–120,000).

Add reviewed `.md` reference files to `knowledge/`. Use clear headings and keep each section focused. For LWF-specific documents, include an owner, review date, version and approved source. Do not place private learner records or secrets in this public/shared corpus.
