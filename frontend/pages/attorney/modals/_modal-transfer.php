<!-- Transfer Attorney Case Modal -->
<div x-show="showTransferModal" x-cloak
     class="fixed inset-0 bg-black/40 flex items-center justify-center z-50"
     @click.self="showTransferModal = false">
    <div class="sp-card" style="width:100%; max-width:460px; margin:16px;" @click.stop>
        <div class="sp-gold-bar"></div>

        <!-- Header -->
        <div style="background:#0F1B2D; padding:16px 20px; display:flex; align-items:center; justify-content:space-between;">
            <h3 style="color:#fff; font-size:15px; font-weight:700; font-family:'IBM Plex Sans',sans-serif; margin:0;">Transfer Case</h3>
            <button @click="showTransferModal = false" style="color:rgba(255,255,255,.5); font-size:18px; cursor:pointer; background:none; border:none;">&times;</button>
        </div>

        <div style="padding:16px 20px; display:flex; flex-direction:column; gap:14px;">

            <!-- Case Info -->
            <div style="background:#fafaf8; border:1px solid #e8e4dc; border-radius:8px; padding:12px 16px;">
                <div style="font-size:11px; color:#8a8a82; text-transform:uppercase; letter-spacing:.5px; margin-bottom:4px; font-family:'IBM Plex Sans',sans-serif;">Transfer From</div>
                <div style="display:flex; align-items:center; gap:8px;">
                    <span class="sp-case-num" x-text="transferForm._caseNumber"></span>
                    <span style="font-size:13px; font-weight:600; color:#1a2535;" x-text="transferForm._clientName"></span>
                    <span style="font-size:11px; color:#8a8a82;">—</span>
                    <span style="font-size:12px; color:#C9A84C; font-weight:600;" x-text="transferForm._currentAttorney"></span>
                </div>
            </div>

            <!-- Transfer To -->
            <div>
                <label style="font-size:11px; font-weight:600; color:#8a8a82; display:block; margin-bottom:4px; font-family:'IBM Plex Sans',sans-serif;">Transfer To *</label>
                <select x-model="transferForm.to_attorney_id" class="sp-select" style="width:100%; padding:8px 12px;">
                    <option value="">Select attorney...</option>
                    <template x-for="u in transferAttorneyList" :key="u.id">
                        <option :value="u.id" x-text="u.display_name || u.full_name"></option>
                    </template>
                </select>
            </div>

            <!-- Note (required) -->
            <div>
                <label style="font-size:11px; font-weight:600; color:#8a8a82; display:block; margin-bottom:4px; font-family:'IBM Plex Sans',sans-serif;">Transfer Note * <span style="font-weight:400; color:#b0a89c;">(reason for transfer)</span></label>
                <textarea x-model="transferForm.note" rows="3" class="sp-search" style="width:100%; padding:8px 12px; resize:none;" placeholder="Why is this case being transferred?"></textarea>
            </div>
        </div>

        <!-- Footer -->
        <div style="display:flex; justify-content:flex-end; gap:8px; padding:12px 20px; background:#fafaf8; border-top:1px solid #f5f2ee;">
            <button @click="showTransferModal = false" class="sp-btn">Cancel</button>
            <button @click="submitTransfer()" :disabled="saving" class="sp-new-btn-navy">
                <span x-show="!saving">Transfer Case</span>
                <span x-show="saving">Transferring...</span>
            </button>
        </div>
    </div>
</div>
