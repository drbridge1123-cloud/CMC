    <style>
    /* ── Deadline Modal (unique styles) ── */
    .dlm-subtitle { font-size: 12px; font-weight: 500; color: var(--gold, #C9A84C); margin-top: 2px; }
    .dlm-input {
        width: 100%; background: #fafafa; border: 1.5px solid var(--border, #d0cdc5); border-radius: 7px;
        padding: 9px 12px; font-size: 13px; color: #1a2535; transition: all .15s; outline: none; font-family: inherit;
    }
    .dlm-input:focus {
        border-color: var(--gold, #C9A84C); background: #fff;
        box-shadow: 0 0 0 3px rgba(201,168,76,.1);
    }
    .dlm-input.dlm-date { font-family: 'IBM Plex Mono', monospace; font-size: 12.5px; }
    .dlm-textarea {
        width: 100%; background: #fafafa; border: 1.5px solid var(--border, #d0cdc5); border-radius: 7px;
        padding: 9px 12px; font-size: 13px; color: #1a2535; transition: all .15s; outline: none; font-family: inherit;
        resize: vertical; min-height: 70px; line-height: 1.5;
    }
    .dlm-textarea:focus {
        border-color: var(--gold, #C9A84C); background: #fff;
        box-shadow: 0 0 0 3px rgba(201,168,76,.1);
    }
    .dlm-textarea::placeholder { color: #c5c5c5; }
    .dlm-current-val { font-size: 13px; font-weight: 500; color: var(--muted, #8a8a82); padding: 10px 0; }
    .dlm-history-item {
        font-size: 12px; border-radius: 6px; padding: 8px 12px;
        background: #fafafa; border: 1px solid var(--border, #d0cdc5);
    }
    .dlm-history-item .dlm-h-meta { display: flex; justify-content: space-between; color: var(--muted, #8a8a82); font-size: 11px; }
    .dlm-history-item .dlm-h-change { color: var(--text, #1a2535); margin-top: 3px; }
    .dlm-history-item .dlm-h-reason { color: var(--muted, #8a8a82); margin-top: 3px; }
    .dlm-history-list { max-height: 120px; overflow-y: auto; display: flex; flex-direction: column; gap: 6px; }
    .dlm-btn-cancel {
        background: #fff; border: 1.5px solid var(--border, #d0cdc5); border-radius: 7px;
        padding: 9px 18px; font-size: 13px; font-weight: 500; color: #5A6B82; cursor: pointer; transition: all .15s;
    }
    .dlm-btn-cancel:hover { background: #f8f7f4; border-color: #ccc; }
    .dlm-btn-submit {
        background: var(--gold, #C9A84C); color: #fff; border: none; border-radius: 7px;
        padding: 9px 22px; font-size: 13px; font-weight: 700; cursor: pointer;
        box-shadow: 0 2px 8px rgba(201,168,76,.35); display: flex; align-items: center; gap: 6px; transition: all .15s;
    }
    .dlm-btn-submit:hover { filter: brightness(1.05); box-shadow: 0 4px 12px rgba(201,168,76,.45); }
    .dlm-btn-submit:disabled { opacity: .55; cursor: not-allowed; }
    </style>

    <!-- Deadline Change Modal -->
    <div x-show="showDeadlineModal" class="sp-modal-overlay"
        style="display:none;" @keydown.escape.window="showDeadlineModal && (showDeadlineModal = false)">
        <div class="fixed inset-0" @click="showDeadlineModal = false"></div>
        <form @submit.prevent="submitDeadlineChange()" class="sp-modal-box sp-modal-box-sm relative z-10" @click.stop>

            <!-- Header -->
            <div class="sp-modal-header">
                <div>
                    <h3 class="sp-modal-title">Change Deadline</h3>
                    <div class="dlm-subtitle" x-text="deadlineProvider?.provider_name"></div>
                </div>
                <button type="button" class="sp-modal-close" @click="showDeadlineModal = false">
                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <!-- Body -->
            <div class="sp-modal-body">
                <div class="sp-form-grid-2">
                    <div>
                        <label class="sp-form-label">Current Deadline</label>
                        <div class="dlm-current-val" x-text="formatDate(deadlineProvider?.deadline) || 'Not set'"></div>
                    </div>
                    <div>
                        <label class="sp-form-label">New Deadline <span class="sp-required-star">*</span></label>
                        <input type="date" x-model="deadlineForm.deadline" required class="dlm-input dlm-date">
                    </div>
                </div>
                <div>
                    <label class="sp-form-label">Reason for Change <span class="sp-required-star">*</span> <span class="sp-form-hint">(min 5 chars)</span></label>
                    <textarea x-model="deadlineForm.reason" rows="3" required minlength="5"
                        placeholder="Why is the deadline being changed?" class="dlm-textarea"></textarea>
                </div>
                <template x-if="deadlineHistory.length > 0">
                    <div>
                        <label class="sp-form-label">Change History</label>
                        <div class="dlm-history-list">
                            <template x-for="dh in deadlineHistory" :key="dh.id">
                                <div class="dlm-history-item">
                                    <div class="dlm-h-meta">
                                        <span x-text="dh.changed_by_name"></span>
                                        <span x-text="timeAgo(dh.created_at)"></span>
                                    </div>
                                    <div class="dlm-h-change">
                                        <span x-text="formatDate(dh.old_deadline)"></span> &rarr; <span x-text="formatDate(dh.new_deadline)"></span>
                                    </div>
                                    <div class="dlm-h-reason" x-text="dh.reason"></div>
                                </div>
                            </template>
                        </div>
                    </div>
                </template>
            </div>

            <!-- Footer -->
            <div class="sp-modal-footer">
                <button type="button" @click="showDeadlineModal = false" class="dlm-btn-cancel">Cancel</button>
                <button type="submit" :disabled="saving || !deadlineForm.deadline || deadlineForm.reason.length < 5" class="dlm-btn-submit">
                    <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    Update Deadline
                </button>
            </div>
        </form>
    </div>
