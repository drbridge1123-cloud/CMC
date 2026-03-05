    <style>
    /* ── Workflow Modal (unique styles) ── */
    .wfm-hint { font-weight: 400; text-transform: none; }
    .wfm-textarea {
        width: 100%; background: #fafafa; border: 1.5px solid var(--border, #d0cdc5); border-radius: 7px;
        padding: 9px 12px; font-size: 13px; color: #1a2535; transition: all .15s; outline: none; font-family: inherit;
        resize: vertical; min-height: 70px; line-height: 1.5;
    }
    .wfm-textarea:focus {
        border-color: var(--gold, #C9A84C); background: #fff;
        box-shadow: 0 0 0 3px rgba(201,168,76,.1);
    }
    .wfm-textarea::placeholder { color: #c5c5c5; }
    .wfm-status-bar {
        display: flex; align-items: center; gap: 12px; padding: 12px 16px;
        background: #fafafa; border: 1.5px solid var(--border, #d0cdc5); border-radius: 8px;
    }
    .wfm-status-bar .wfm-from { font-size: 13px; font-weight: 600; color: var(--muted, #8a8a82); }
    .wfm-status-bar .wfm-to { font-size: 13px; font-weight: 700; }
    .wfm-btn-cancel {
        background: #fff; border: 1.5px solid var(--border, #d0cdc5); border-radius: 7px;
        padding: 9px 18px; font-size: 13px; font-weight: 500; color: #5A6B82; cursor: pointer; transition: all .15s;
    }
    .wfm-btn-cancel:hover { background: #f8f7f4; border-color: #ccc; }
    .wfm-btn-submit {
        border: none; border-radius: 7px; padding: 9px 22px; font-size: 13px; font-weight: 700;
        cursor: pointer; display: flex; align-items: center; gap: 6px; transition: all .15s; color: #fff;
    }
    .wfm-btn-submit:hover { filter: brightness(1.08); }
    .wfm-btn-submit:disabled { opacity: .75; cursor: not-allowed; filter: saturate(.5); }
    </style>

    <!-- Unified Status Change Modal -->
    <div x-show="showStatusChangeModal" class="sp-modal-overlay"
        style="display:none;" @keydown.escape.window="showStatusChangeModal && (showStatusChangeModal = false)">
        <div class="fixed inset-0" @click="showStatusChangeModal = false"></div>
        <form @submit.prevent="submitStatusChange()" class="sp-modal-box relative z-10" @click.stop>
            <div class="sp-modal-header"
                :style="statusChangeForm.direction === 'forward' ? 'background:var(--gold, #C9A84C);'
                    : statusChangeForm.direction === 'reassign' ? 'background:#2563eb;'
                    : 'background:#ea580c;'">
                <h3 class="sp-modal-title" :style="statusChangeForm.direction === 'forward' ? 'color:#0F1B2D;' : 'color:#fff;'"
                    x-text="statusChangeForm.direction === 'forward' ? 'Move Case Forward'
                        : statusChangeForm.direction === 'reassign' ? 'Reassign Case'
                        : 'Send Case Back'"></h3>
                <button type="button" class="sp-modal-close"
                    :style="statusChangeForm.direction === 'forward' ? 'color:rgba(15,27,45,.4);' : ''"
                    @click="showStatusChangeModal = false">
                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="sp-modal-body">
                <template x-if="statusChangeForm.direction !== 'reassign'">
                    <div class="wfm-status-bar">
                        <span class="wfm-from" x-text="statusChangeForm.from_label"></span>
                        <svg width="18" height="18" fill="none"
                            :stroke="statusChangeForm.direction === 'forward' ? 'var(--gold, #C9A84C)' : '#ea580c'"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                        </svg>
                        <span class="wfm-to"
                            :style="'color:' + (statusChangeForm.direction === 'forward' ? 'var(--gold, #C9A84C)' : '#ea580c')"
                            x-text="statusChangeForm.to_label"></span>
                    </div>
                </template>
                <template x-if="statusChangeForm.direction === 'reassign'">
                    <div class="wfm-status-bar" style="justify-content:center;">
                        <span style="font-size:13px; font-weight:600; color:#2563eb;" x-text="statusChangeForm.to_label"></span>
                        <span style="font-size:11px; color:var(--muted, #8a8a82); margin-left:4px;">— reassign to another staff</span>
                    </div>
                </template>
                <div>
                    <label class="sp-form-label">Assign To <span style="color:var(--gold);">*</span></label>
                    <select x-model="statusChangeForm.assign_to" class="wfm-textarea" style="min-height:auto; padding:9px 12px;">
                        <option value="">-- Select Staff --</option>
                        <template x-for="s in staffList" :key="s.id">
                            <option :value="String(s.id)" x-text="s.display_name || s.full_name"></option>
                        </template>
                    </select>
                </div>
                <div>
                    <label class="sp-form-label">Note <span style="color:var(--gold);">*</span> <span class="wfm-hint">(min 5 chars)</span></label>
                    <textarea x-model="statusChangeForm.note" required rows="3" minlength="5"
                        placeholder="Describe why this change is being made..." class="wfm-textarea"></textarea>
                </div>
            </div>
            <div class="sp-modal-footer">
                <button type="button" @click="showStatusChangeModal = false" class="wfm-btn-cancel">Cancel</button>
                <button type="submit" :disabled="saving || statusChangeForm.note.trim().length < 5 || !statusChangeForm.assign_to" class="wfm-btn-submit"
                    :style="statusChangeForm.direction === 'forward'
                        ? 'background:var(--gold, #C9A84C); color:#0F1B2D; box-shadow:0 2px 8px rgba(201,168,76,.35);'
                        : statusChangeForm.direction === 'reassign'
                            ? 'background:#2563eb; box-shadow:0 2px 8px rgba(37,99,235,.3);'
                            : 'background:#ea580c; box-shadow:0 2px 8px rgba(234,88,12,.3);'">
                    <template x-if="statusChangeForm.direction === 'forward'">
                        <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                    </template>
                    <template x-if="statusChangeForm.direction === 'backward'">
                        <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/></svg>
                    </template>
                    <template x-if="statusChangeForm.direction === 'reassign'">
                        <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    </template>
                    <span x-text="statusChangeForm.direction === 'forward' ? 'Confirm'
                        : statusChangeForm.direction === 'reassign' ? 'Reassign'
                        : 'Send Back'"></span>
                </button>
            </div>
        </form>
    </div>
