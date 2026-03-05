<!-- Send to Billing Final Modal -->
<div x-show="showSendBillingModal" x-cloak
     class="sp-modal-overlay"
     @click.self="showSendBillingModal = false">
    <div class="sp-modal-box" @click.stop>
        <!-- Header -->
        <div class="sp-modal-header">
            <h3 class="sp-modal-title">Send to Billing Review</h3>
            <button @click="showSendBillingModal = false" class="sp-modal-close">&times;</button>
        </div>

        <div class="sp-modal-body">

            <!-- Case Info -->
            <div style="background:#fafaf8; border:1px solid #e8e4dc; border-radius:8px; padding:12px 16px;">
                <div style="font-size:11px; color:#8a8a82; text-transform:uppercase; letter-spacing:.5px; margin-bottom:4px; font-family:'IBM Plex Sans',sans-serif;">Attorney Case</div>
                <div style="display:flex; align-items:center; gap:8px;">
                    <span class="sp-case-num" x-text="sendBillingForm._caseNumber"></span>
                    <span style="font-size:13px; font-weight:600; color:#1a2535;" x-text="sendBillingForm._clientName"></span>
                </div>
            </div>

            <!-- Assign To -->
            <div>
                <label style="font-size:11px; font-weight:600; color:#8a8a82; display:block; margin-bottom:4px; font-family:'IBM Plex Sans',sans-serif;">Assign To *</label>
                <select x-model="sendBillingForm.assigned_to" class="sp-select" style="width:100%; padding:8px 12px;">
                    <template x-for="u in billingStaff" :key="u.id">
                        <option :value="u.id" x-text="u.display_name || u.full_name"></option>
                    </template>
                </select>
            </div>

            <!-- Info box -->
            <div style="background:rgba(201,168,76,.06); border:1px solid rgba(201,168,76,.2); border-radius:8px; padding:10px 14px; font-size:11px; color:#8a6d1b; line-height:1.5;">
                Billing staff will verify final balances before the case moves to accounting.
            </div>

            <!-- Note -->
            <div>
                <label style="font-size:11px; font-weight:600; color:#8a8a82; display:block; margin-bottom:4px; font-family:'IBM Plex Sans',sans-serif;">Note (optional)</label>
                <textarea x-model="sendBillingForm.note" rows="2" class="sp-search" style="width:100%; padding:8px 12px; resize:none;" placeholder="Optional note..."></textarea>
            </div>
        </div>

        <!-- Footer -->
        <div class="sp-modal-footer">
            <button @click="showSendBillingModal = false" class="sp-btn">Cancel</button>
            <button @click="submitSendToBilling()" :disabled="saving" class="sp-new-btn" style="background:#C9A84C; border-color:#C9A84C;">
                <span x-show="!saving">Send to Billing</span>
                <span x-show="saving">Sending...</span>
            </button>
        </div>
    </div>
</div>
