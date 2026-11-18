<?php
declare(strict_types=1);
require_once __DIR__ . '/lib/bootstrap.php';
$csrf = app_csrf_token();
$config = app_public_config();
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="theme-color" content="#101b31">
  <title>LWF AI Assistant</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Space+Grotesk:wght@500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="assets/app.css">
</head>
<body>
<div class="app-shell">
  <aside class="sidebar" id="sidebar">
    <a class="brand" href="./"><span class="brand-icon">L</span><span><b>Learn With Flevian</b><small>AI KNOWLEDGE ASSISTANT</small></span></a>
    <div class="sidebar-label">WORKSPACE</div>
    <button class="new-chat" id="newChat"><span>＋</span> New conversation</button>
    <div class="sidebar-label recent-label">RECENT CHATS</div>
    <nav class="chat-list" id="chatList"><div class="chat-list-empty">Your conversations will appear here.</div></nav>
    <div class="sidebar-card">
      <div class="side-card-icon">✦</div>
      <div><b>Knowledge grounded</b><p>Retrieves from <?= (int)$config['knowledge_files'] ?> curated platform, learning and engineering references.</p></div>
    </div>
    <div class="sidebar-bottom">
      <div class="status-row"><i class="status-dot"></i><span>Assistant interface ready</span></div>
      <small>MODULE 06 · OPENROUTER · <?= (int)$config['knowledge_files'] ?> KNOWLEDGE FILES</small>
    </div>
  </aside>

  <main class="main">
    <header class="topbar">
      <button class="mobile-menu" id="menuBtn" aria-label="Open menu">☰</button>
      <div class="top-title"><span class="crumb">WORKSPACE</span><b>AI Assistant</b></div>
      <div class="top-actions">
        <span class="secure-pill"><i></i> SECURE SERVER CONNECTION</span>
        <button class="clear-btn" id="clearChat" title="Clear current conversation">Clear chat</button>
      </div>
    </header>

    <section class="chat-area">
      <div class="welcome" id="welcome">
        <div class="welcome-mark"><span>✦</span></div>
        <div class="welcome-kicker">LWF INTELLIGENCE WORKSPACE</div>
        <h1>What would you like<br><em>to understand today?</em></h1>
        <p>Explore LWF guidance, academic concepts and practical software engineering. The assistant retrieves relevant passages from its local knowledge library and keeps your conversation history.</p>
        <div class="mode-picker">
          <button class="mode-option active" data-mode="lms" type="button"><span class="mode-icon">▤</span><span><b>LWF LMS mode</b><small>Prioritise LWF documentation and workflows</small></span><i>✓</i></button>
          <button class="mode-option" data-mode="general" type="button"><span class="mode-icon">✧</span><span><b>General mode</b><small>Broader questions beyond LWF</small></span><i></i></button>
        </div>
        <div class="suggestions">
          <button class="suggestion" data-prompt="How do I get started using the LWF learning platform?"><span>↗</span> Getting started with LWF</button>
          <button class="suggestion" data-prompt="Explain the difference between a course, a module and a lesson."><span>⌘</span> Understand learning structure</button>
          <button class="suggestion" data-prompt="What should I do if I cannot find the information I need?"><span>?</span> Finding the right support</button>
        </div>
      </div>

      <div class="conversation hidden" id="conversation" aria-live="polite"></div>
      <div class="typing hidden" id="typing"><span class="typing-avatar">✦</span><div class="typing-bubble"><i></i><i></i><i></i><span>Preparing a response…</span></div></div>
    </section>

    <section class="composer-wrap">
      <div class="attachment-strip" id="attachmentStrip"></div>
      <div class="composer" id="composer">
        <textarea id="prompt" rows="1" maxlength="<?= (int)$config['max_input_chars'] ?>" placeholder="Message the LWF assistant…" aria-label="Your message"></textarea>
        <div class="composer-tools">
          <div class="composer-meta">
            <label class="attach-btn" for="fileInput" title="Attach files up to 5 MB"><span>＋</span> Attach</label>
            <input type="file" id="fileInput" hidden multiple accept=".pdf,.png,.jpg,.jpeg,.webp,.txt,.md,.csv,.json,.xml,.log">
            <span class="mode-indicator" id="modeIndicator">▤ LWF LMS MODE</span><span id="charCounter">0 / <?= (int)$config['max_input_chars'] ?></span>
          </div>
          <button class="send-btn" id="sendBtn" type="button" disabled aria-label="Send message"><span class="send-label">Send</span><span class="send-arrow">↑</span></button>
        </div>
      </div>
      <div class="disclaimer">AI responses can be inaccurate. Verify important information against official LWF documentation.</div>
    </section>
  </main>
</div>
<div class="toast hidden" id="toast"></div>
<script>
window.LWF_CHAT_CONFIG = {
  csrf: <?= json_encode($csrf) ?>,
  maxInput: <?= (int)$config['max_input_chars'] ?>,
  defaultMode: <?= json_encode($config['default_mode']) ?>,
  maxFileBytes: 5242880
};
</script>
<script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/dompurify@3.2.6/dist/purify.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/highlight.js@11.11.1/lib/common.min.js"></script>
<script src="assets/app.js"></script>
</body>
</html>
