<!-- Send to Billing Final Modal -->
<div x-show="showSendBillingModal" x-cloak
     class="fixed inset-0 bg-black/40 flex items-center justify-center z-50"
     @click.self="showSendBillingModal = false">
    <div class="sp-card" style="width:100%; max-width:440px; margin:16px;" @click.stop>
        <div class="sp-gold-bar"></div>

        <!-- Header -->
        <div style="background:#0F1B2D; padding:16px 20px; display:flex; align-items:center; justify-content:space-between;">
            <h3 style="color:#fff; font-size:15px; font-weight:700; font-family:'IBM Plex Sans',sans-serif; margin:0;">Send to Billing Review</h3>
            <button @click="showSendBillingModal = false" style="color:rgba(255,255,255,.5); font-size:18px; cursor:pointer; background:none; border:none;">&times;</button>
        </div>

        <div style="padding:16px 20px; display:flex; flex-direction:column; gap:14px;">

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
        <div style="display:flex; justify-content:flex-end; gap:8px; padding:12px 20px; background:#fafaf8; border-top:1px solid #f5f2ee;">
            <button @click="showSendBillingModal = false" class="sp-btn">Cancel</button>
            <button @click="submitSendToBilling()" :disabled="saving" class="sp-new-btn" style="background:#C9A84C; border-color:#C9A84C;">
                <span x-show="!saving">Send to Billing</span>
                <span x-show="saving">Sending...</span>
            </button>
        </div>
    </div>
</div>
