(() => {
  const cfg=window.LWF_CHAT_CONFIG, $=id=>document.getElementById(id);
  const welcome=$('welcome'),conversation=$('conversation'),prompt=$('prompt'),sendBtn=$('sendBtn');
  const typing=$('typing'),charCounter=$('charCounter'),fileInput=$('fileInput');
  let mode=cfg.defaultMode||'lms',busy=false,chatId=null,attachments=[],conversations=[];

  const esc=s=>String(s).replace(/[&<>"']/g,c=>({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;'}[c]));
  function toast(msg){const el=$('toast');el.textContent=msg;el.classList.remove('hidden');clearTimeout(toast.t);toast.t=setTimeout(()=>el.classList.add('hidden'),4000);}
  async function api(url,opts={}) {
    const res=await fetch(url,{...opts,headers:{...(opts.headers||{}),'X-CSRF-Token':cfg.csrf}});
    let data;try{data=await res.json();}catch{throw new Error(`Server returned an invalid response (${res.status}).`);}
    if(!res.ok||data.ok===false)throw new Error(data.error?.message||`Request failed (${res.status}).`);
    return data;
  }
  function updateMode(next){mode=next;document.querySelectorAll('.mode-option').forEach(b=>{b.classList.toggle('active',b.dataset.mode===mode);b.querySelector('i').textContent=b.dataset.mode===mode?'✓':'';});$('modeIndicator').textContent=mode==='lms'?'▤ LWF LMS MODE':'✧ GENERAL MODE';}
  function renderMarkdown(text){
    if(!window.marked||!window.DOMPurify)return `<p>${esc(text).replace(/\n/g,'<br>')}</p>`;
    marked.setOptions({gfm:true,breaks:true,headerIds:false,mangle:false});
    const html=marked.parse(text);
    return DOMPurify.sanitize(html,{USE_PROFILES:{html:true}});
  }
  function addMessage(role,content,sources=[],files=[],model=null){
    welcome.classList.add('hidden');conversation.classList.remove('hidden');
    const row=document.createElement('article');row.className=`message ${role}`;
    const avatar=document.createElement('div');avatar.className='message-avatar';avatar.textContent=role==='assistant'?'✦':'Y';
    const body=document.createElement('div');body.className='message-body';
    const label=document.createElement('div');label.className='message-label';label.textContent=role==='assistant'?'LWF ASSISTANT':'YOU';
    const bubble=document.createElement('div');bubble.className='message-content';
    if(role==='assistant'){bubble.innerHTML=renderMarkdown(content);bubble.querySelectorAll('pre code').forEach(el=>{if(window.hljs)hljs.highlightElement(el);});}
    else bubble.textContent=content;
    body.append(label,bubble);
    if(files.length){const chips=document.createElement('div');chips.className='message-files';chips.textContent='Attachments: '+files.join(', ');body.append(chips);}
    if(role==='assistant'&&sources.length){const src=document.createElement('div');src.className='source-list';src.textContent='Sources: '+sources.join(' · ');body.append(src);}
    if(role==='assistant'&&model){const meta=document.createElement('div');meta.className='model-meta';meta.textContent='Model: '+model;body.append(meta);}
    row.append(avatar,body);conversation.append(row);row.scrollIntoView({behavior:'smooth',block:'end'});
  }
  function setBusy(v){
    busy=v;sendBtn.disabled=v||(!prompt.value.trim()&&attachments.length===0);
    sendBtn.classList.toggle('loading',v);sendBtn.innerHTML=v?'<span class="loader"></span><span>Thinking</span>':'<span class="send-label">Send</span><span class="send-arrow">↑</span>';
    typing.classList.toggle('hidden',!v);
  }
  function resizeInput(){prompt.style.height='auto';prompt.style.height=Math.min(prompt.scrollHeight,150)+'px';sendBtn.disabled=busy||(!prompt.value.trim()&&attachments.length===0);}
  function renderAttachmentStrip(){
    const strip=$('attachmentStrip');strip.innerHTML='';
    attachments.forEach(a=>{const chip=document.createElement('span');chip.className='attachment-chip';chip.innerHTML=`<b>${a.kind==='pdf'?'PDF':a.kind==='image'?'IMG':'TXT'}</b> ${esc(a.name)} <button type="button" aria-label="Remove attachment">×</button>`;chip.querySelector('button').addEventListener('click',async()=>{try{await api('api/remove-attachment.php',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({chat_id:chatId,attachment_id:a.id})});attachments=attachments.filter(x=>x.id!==a.id);renderAttachmentStrip();resizeInput();}catch(e){toast(e.message);}});strip.append(chip);});
    strip.classList.toggle('visible',attachments.length>0);
  }
  async function refreshConversations(){
    try{
      const data=await api('api/conversations.php');conversations=data.conversations||[];
      const list=$('chatList');list.innerHTML='';
      if(!conversations.length){list.innerHTML='<div class="chat-list-empty">No conversations yet.</div>';return;}
      conversations.forEach(c=>{
        const item=document.createElement('button');item.className='chat-list-item'+(c.id===chatId?' active':'');item.type='button';
        item.innerHTML=`<span class="chat-list-icon">◷</span><span class="chat-list-title">${esc(c.title||'Conversation')}</span><span class="chat-delete" title="Delete conversation">×</span>`;
        item.addEventListener('click',e=>{if(e.target.classList.contains('chat-delete')){e.stopPropagation();deleteConversation(c.id);}else loadConversation(c.id);});
        list.append(item);
      });
    }catch(e){$('chatList').innerHTML='<div class="chat-list-empty">Conversation list unavailable.</div>';}
  }
  async function createConversation(){
    const data=await api('api/new.php',{method:'POST'});chatId=data.chat.id;attachments=[];renderAttachmentStrip();
    conversation.innerHTML='';conversation.classList.add('hidden');welcome.classList.remove('hidden');updateMode(cfg.defaultMode||'lms');await refreshConversations();prompt.focus();
  }
  async function loadConversation(id){
    if(busy)return;
    try{
      const data=await api('api/conversation.php?id='+encodeURIComponent(id));const c=data.chat;
      chatId=c.id;mode=c.mode||'lms';updateMode(mode);attachments=c.attachments||[];renderAttachmentStrip();
      conversation.innerHTML='';welcome.classList.add('hidden');conversation.classList.remove('hidden');
      c.messages.forEach(m=>addMessage(m.role,m.content,m.sources||[],m.attachments||[],m.model||null));
      await refreshConversations();$('sidebar').classList.remove('open');
    }catch(e){toast(e.message);}
  }
  async function deleteConversation(id){
    if(busy)return;
    if(!confirm('Delete this conversation and its uploaded attachments?'))return;
    try{await api('api/delete.php',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({id})});if(chatId===id)await createConversation();else await refreshConversations();}
    catch(e){toast(e.message);}
  }
  async function uploadFiles(files){
    if(!chatId)await createConversation();
    const selected=Array.from(files);
    for(const f of selected){if(f.size>cfg.maxFileBytes){toast(`${f.name}: maximum file size is 5 MB.`);return;}}
    const fd=new FormData();fd.append('chat_id',chatId);selected.forEach(f=>fd.append('files[]',f));
    const btn=document.querySelector('.attach-btn');btn.classList.add('uploading');btn.setAttribute('aria-busy','true');
    try{
      const data=await api('api/upload.php',{method:'POST',body:fd});
      attachments.push(...data.attachments);renderAttachmentStrip();resizeInput();toast(`${data.attachments.length} attachment(s) added.`);
    }catch(e){toast(e.message);}
    finally{btn.classList.remove('uploading');btn.removeAttribute('aria-busy');fileInput.value='';}
  }
  async function sendMessage(text=prompt.value){
    const message=text.trim();if((!message&&!attachments.length)||busy)return;
    if(!chatId)await createConversation();
    const fileNames=attachments.map(a=>a.name);
    addMessage('user',message||'Please review the attached file(s).',[],fileNames);
    prompt.value='';resizeInput();charCounter.textContent=`0 / ${cfg.maxInput}`;setBusy(true);
    try{
      const data=await api('api/chat.php',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({chat_id:chatId,message:message||'Please review the attached file(s) and help me understand them.',mode})});
      addMessage('assistant',data.answer,data.sources||[],[],data.served_model||null);
      await refreshConversations();
    }catch(e){addMessage('assistant',`I couldn't complete that request.\n\n${e.message}`);toast(e.message);}
    finally{setBusy(false);prompt.focus();}
  }
  async function resetChat(){if(busy)return;try{if(chatId)await api('api/delete.php',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({id:chatId})});await createConversation();}catch(e){toast(e.message);}}
  document.querySelectorAll('.mode-option').forEach(b=>b.addEventListener('click',()=>updateMode(b.dataset.mode)));
  document.querySelectorAll('.suggestion').forEach(b=>b.addEventListener('click',()=>sendMessage(b.dataset.prompt)));
  prompt.addEventListener('input',()=>{resizeInput();charCounter.textContent=`${prompt.value.length} / ${cfg.maxInput}`;});
  prompt.addEventListener('keydown',e=>{if(e.key==='Enter'&&!e.shiftKey){e.preventDefault();sendMessage();}});
  sendBtn.addEventListener('click',()=>sendMessage());
  fileInput.addEventListener('change',()=>{if(fileInput.files.length)uploadFiles(fileInput.files);});
  $('newChat').addEventListener('click',()=>createConversation().catch(e=>toast(e.message)));
  $('clearChat').addEventListener('click',resetChat);
  $('menuBtn').addEventListener('click',()=>$('sidebar').classList.toggle('open'));
  (async()=>{await refreshConversations();if(conversations.length)await loadConversation(conversations[0].id);else await createConversation();})();
  updateMode(mode);resizeInput();
})();
