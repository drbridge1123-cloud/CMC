<!-- All sp- styles loaded from shared sp-design-system.css -->

<style>
/* ── Message Modal ── */
.msm { width: 520px; border-radius: 12px; box-shadow: 0 24px 64px rgba(0,0,0,.24); overflow: hidden; background: #fff; }
.msm-header { background: #0F1B2D; padding: 18px 24px; display: flex; align-items: center; justify-content: space-between; flex-shrink: 0; }
.msm-header h3 { font-size: 15px; font-weight: 700; color: #fff; margin: 0; }
.msm-close { background: none; border: none; color: rgba(255,255,255,.35); cursor: pointer; padding: 4px; transition: color .15s; }
.msm-close:hover { color: rgba(255,255,255,.75); }
.msm-body { padding: 24px; display: flex; flex-direction: column; gap: 16px; max-height: 70vh; overflow-y: auto; }
.msm-label { display: block; font-size: 9.5px; font-weight: 700; color: #8a8a82; text-transform: uppercase; letter-spacing: .08em; margin-bottom: 5px; }
.msm-input, .msm-select, .msm-textarea {
    width: 100%; background: #fafafa; border: 1.5px solid #d0cdc5; border-radius: 7px;
    padding: 9px 12px; font-size: 13px; color: #1a2535; transition: all .15s; outline: none; font-family: inherit;
}
.msm-input:focus, .msm-select:focus, .msm-textarea:focus {
    border-color: #C9A84C; background: #fff;
    box-shadow: 0 0 0 3px rgba(201,168,76,.1);
}
.msm-select {
    appearance: none; cursor: pointer; padding-right: 30px;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%238a8a82' stroke-width='2.5' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M6 9l6 6 6-6'/%3E%3C/svg%3E");
    background-repeat: no-repeat; background-position: right 10px center;
}
.msm-textarea { resize: vertical; min-height: 120px; line-height: 1.5; }
.msm-footer { padding: 14px 24px; border-top: 1px solid #d0cdc5; display: flex; justify-content: flex-end; gap: 10px; flex-shrink: 0; }
.msm-btn-cancel {
    background: #fff; border: 1.5px solid #d0cdc5; border-radius: 7px;
    padding: 9px 18px; font-size: 13px; font-weight: 500; color: #5A6B82; cursor: pointer; transition: all .15s;
}
.msm-btn-cancel:hover { background: #f8f7f4; border-color: #ccc; }
.msm-btn-submit {
    background: #C9A84C; color: #fff; border: none; border-radius: 7px;
    padding: 9px 22px; font-size: 13px; font-weight: 700; cursor: pointer;
    box-shadow: 0 2px 8px rgba(201,168,76,.35); display: flex; align-items: center; gap: 6px; transition: all .15s;
}
.msm-btn-submit:hover { filter: brightness(1.05); box-shadow: 0 4px 12px rgba(201,168,76,.45); }
.msm-btn-submit:disabled { opacity: .6; cursor: not-allowed; }

/* ── Message View Modal ── */
.mvm-from { font-size: 12px; color: #8a8a82; }
.mvm-from strong { color: #1a2535; font-weight: 600; }
.mvm-body { background: #fafaf8; border: 1px solid #e8e4dc; border-radius: 8px; padding: 14px 16px; font-size: 13px; color: #1a2535; line-height: 1.6; white-space: pre-wrap; }

/* ── Message row ── */
.msg-row { border-left: 3px solid transparent; cursor: pointer; transition: all .1s; }
.msg-row:hover { background: rgba(201,168,76,.04) !important; border-left-color: #C9A84C; }
.msg-row-unread { background: rgba(59,130,246,.03); }
.msg-unread-dot { width: 7px; height: 7px; background: #3b82f6; border-radius: 50%; display: inline-block; }
</style>

<div x-data="messagesPage()" x-init="init()">

    <!-- ═══ Unified Card ═══ -->
    <div class="sp-card">
        <div class="sp-gold-bar"></div>

        <!-- Header -->
        <div class="sp-header">
            <div style="flex:1; display:flex; align-items:center; gap:12px;">
                <span class="sp-title" style="font-size:14px;">Messages</span>
                <span x-show="unreadCount > 0" style="background:#ef4444; color:#fff; font-size:10px; font-weight:700; padding:2px 8px; border-radius:10px;" x-text="unreadCount + ' unread'"></span>
            </div>
            <button @click="markAllRead()" x-show="unreadCount > 0" class="sp-btn" style="font-size:11px; padding:5px 12px;">Mark All Read</button>
            <button @click="openComposeModal()" class="sp-new-btn-navy">+ New Message</button>
        </div>

        <!-- Toolbar / Tabs -->
        <div class="sp-toolbar">
            <div class="sp-tabs">
                <button class="sp-tab" :class="filter === 'all' && 'on'" @click="filter = 'all'; loadMessages()">All</button>
                <button class="sp-tab" :class="filter === 'unread' && 'on'" @click="filter = 'unread'; loadMessages()">Unread</button>
                <button class="sp-tab" :class="filter === 'sent' && 'on'" @click="filter = 'sent'; loadMessages()">Sent</button>
            </div>
        </div>

        <!-- Loading -->
        <div x-show="loading" class="sp-loading" style="padding:32px 0;">Loading messages...</div>

        <!-- Empty -->
        <div x-show="!loading && messages.length === 0" class="sp-empty" style="padding:32px 0;">No messages</div>

        <!-- Messages Table -->
        <div x-show="!loading && messages.length > 0" x-cloak>
            <table class="sp-table">
                <thead>
                    <tr>
                        <th style="width:24px;"></th>
                        <th style="width:140px;">From / To</th>
                        <th>Subject</th>
                        <th style="width:100px; text-align:right;">Time</th>
                        <th style="width:40px;"></th>
                    </tr>
                </thead>
                <tbody>
                    <template x-for="(msg, idx) in messages" :key="msg.id + '-' + idx">
                        <tr @click="viewMessage(msg)" class="msg-row" :class="msg.direction === 'received' && !parseInt(msg.is_read) ? 'msg-row-unread' : ''">
                            <!-- Direction -->
                            <td style="text-align:center; padding-left:12px;">
                                <span x-show="msg.direction === 'received'" style="color:#3b82f6; font-size:13px;" title="Received">↓</span>
                                <span x-show="msg.direction === 'sent'" style="color:#10b981; font-size:13px;" title="Sent">↑</span>
                            </td>
                            <!-- From/To -->
                            <td>
                                <div style="display:flex; align-items:center; gap:6px;">
                                    <span x-show="msg.direction === 'received' && !parseInt(msg.is_read)" class="msg-unread-dot"></span>
                                    <span x-show="msg.direction === 'received'" style="font-size:13px;" :style="!parseInt(msg.is_read) ? 'font-weight:700; color:#1a2535;' : 'color:#3D4F63;'" x-text="msg.from_name"></span>
                                    <span x-show="msg.direction === 'sent'" style="font-size:13px; color:#8a8a82;">To: <span style="color:#3D4F63;" x-text="msg.to_name"></span></span>
                                </div>
                            </td>
                            <!-- Subject -->
                            <td>
                                <span style="font-size:13px; display:block; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; max-width:400px;"
                                      :style="msg.direction === 'received' && !parseInt(msg.is_read) ? 'font-weight:600; color:#1a2535;' : 'color:#3D4F63;'"
                                      x-text="msg.subject"></span>
                            </td>
                            <!-- Time -->
                            <td style="text-align:right;">
                                <span class="sp-mono" style="font-size:11px; color:#9ca3af;" x-text="timeAgo(msg.created_at)"></span>
                            </td>
                            <!-- Delete -->
                            <td style="text-align:center;">
                                <button x-show="msg.direction === 'received'" @click.stop="deleteMessage(msg.id)" title="Delete"
                                        class="sp-act sp-act-red" style="opacity:.4; transition:opacity .15s;"
                                        onmouseover="this.style.opacity='1'" onmouseout="this.style.opacity='.4'">
                                    <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
            <div style="padding:10px 24px; border-top:1px solid #e8e4dc; font-size:13px; color:#9ca3af;">
                Showing <span x-text="messages.length"></span> message<span x-text="messages.length === 1 ? '' : 's'"></span>
            </div>
        </div>
    </div><!-- /sp-card -->

    <!-- ═══ View Message Modal ═══ -->
    <template x-if="showViewModal">
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4" @keydown.escape.window="closeViewModal()">
            <div class="fixed inset-0" style="background:rgba(0,0,0,.45);" @click="closeViewModal()"></div>
            <div class="msm relative z-10" @click.stop>
                <div class="msm-header">
                    <h3>Message</h3>
                    <button type="button" class="msm-close" @click="closeViewModal()">
                        <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                <div class="msm-body">
                    <div style="display:flex; align-items:center; justify-content:space-between;">
                        <template x-if="viewingMessage.direction === 'received'">
                            <div class="mvm-from">From: <strong x-text="viewingMessage.from_name"></strong></div>
                        </template>
                        <template x-if="viewingMessage.direction === 'sent'">
                            <div class="mvm-from">To: <strong x-text="viewingMessage.to_name"></strong></div>
                        </template>
                        <span class="sp-mono" style="font-size:11px; color:#9ca3af;" x-text="timeAgo(viewingMessage.created_at)"></span>
                    </div>
                    <div style="font-size:14px; font-weight:600; color:#1a2535;" x-text="viewingMessage.subject"></div>
                    <div class="mvm-body" x-text="viewingMessage.message"></div>
                </div>
                <div class="msm-footer">
                    <button @click="closeViewModal()" class="msm-btn-cancel">Close</button>
                    <template x-if="viewingMessage.direction === 'received'">
                        <button @click="replyToMessage()" class="msm-btn-submit">
                            <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10l9-7v4c8 0 9 7 9 7s-2-4-9-4v4l-9-7z"/></svg>
                            Reply
                        </button>
                    </template>
                </div>
            </div>
        </div>
    </template>

    <!-- ═══ Compose Modal ═══ -->
    <template x-if="showComposeModal">
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4" @keydown.escape.window="closeComposeModal()">
            <div class="fixed inset-0" style="background:rgba(0,0,0,.45);" @click="closeComposeModal()"></div>
            <div class="msm relative z-10" @click.stop>
                <div class="msm-header">
                    <h3 x-text="composeForm.replyTo ? 'Reply' : 'New Message'"></h3>
                    <button type="button" class="msm-close" @click="closeComposeModal()">
                        <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                <div class="msm-body">
                    <div>
                        <label class="msm-label">To <span style="color:#C9A84C;">*</span></label>
                        <select x-model="composeForm.to_user_id" class="msm-select">
                            <option value="">Select recipient...</option>
                            <template x-for="u in staffList" :key="u.id">
                                <option :value="u.id" x-text="u.display_name || u.full_name" :selected="composeForm.to_user_id == u.id"></option>
                            </template>
                        </select>
                    </div>
                    <div>
                        <label class="msm-label">Subject <span style="color:#C9A84C;">*</span></label>
                        <input type="text" x-model="composeForm.subject" maxlength="200" class="msm-input" placeholder="Message subject">
                    </div>
                    <div>
                        <label class="msm-label">Message <span style="color:#C9A84C;">*</span></label>
                        <textarea x-model="composeForm.message" rows="5" maxlength="5000" class="msm-textarea" placeholder="Type your message..."></textarea>
                    </div>
                </div>
                <div class="msm-footer">
                    <button @click="closeComposeModal()" class="msm-btn-cancel">Cancel</button>
                    <button @click="sendMessage()" :disabled="sending" class="msm-btn-submit">
                        <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                        <span x-text="sending ? 'Sending...' : 'Send'"></span>
                    </button>
                </div>
            </div>
        </div>
    </template>

</div>
