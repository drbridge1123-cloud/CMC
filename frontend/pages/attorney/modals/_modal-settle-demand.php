<!-- Settle Demand Modal -->
<div x-show="showSettleDemandModal" x-cloak class="sp-modal-overlay" @click.self="showSettleDemandModal = false">
    <div class="sp-modal-box" @click.stop>
        <div class="sp-modal-header">
            <h3 class="sp-modal-title">Settle Demand Case</h3>
            <div style="font-size:12px; color:#C9A84C; margin-top:4px;">
                <span x-text="settlingCase?.case_number" style="font-weight:600;"></span> — <span x-text="settlingCase?.client_name"></span>
            </div>
        </div>
        <div class="sp-modal-body">
            <div class="sp-form-grid-2">
                <div :style="hasCommission ? '' : 'grid-column:span 2;'">
                    <label class="sp-form-label">Settled Amount ($) *</label>
                    <input type="number" x-model.number="settleForm.settled" step="0.01" min="0" required class="sp-search" style="width:100%;">
                </div>
                <div x-show="hasCommission">
                    <label class="sp-form-label">Legal Fee (33.33%)</label>
                    <div style="padding:8px 12px; background:#fafaf8; border:1px solid #e8e4dc; border-radius:8px; font-size:13px; font-family:'IBM Plex Mono',monospace; color:#1a2535;"
                         x-text="'$' + ((settleForm.settled || 0) / 3).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})"></div>
                </div>
            </div>
            <template x-if="hasCommission">
                <div class="sp-form-grid-2">
                    <div>
                        <label class="sp-form-label">Disc. Legal Fee ($) *</label>
                        <input type="number" x-model.number="settleForm.discounted_legal_fee" step="0.01" min="0" required class="sp-search" style="width:100%;">
                    </div>
                    <div>
                        <label class="sp-form-label">Commission (5%)</label>
                        <div style="padding:8px 12px; background:rgba(21,128,61,.06); border:1px solid rgba(21,128,61,.2); border-radius:8px; font-size:13px; font-family:'IBM Plex Mono',monospace; font-weight:700; color:#15803d;"
                             x-text="'$' + ((settleForm.discounted_legal_fee || 0) * 0.05).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})"></div>
                    </div>
                </div>
            </template>
            <template x-if="hasCommission">
                <div>
                    <label class="sp-form-label">Month</label>
                    <input type="text" x-model="settleForm.month" placeholder="e.g. Feb. 2025" class="sp-search" style="width:100%;">
                </div>
            </template>
            <div style="display:flex; align-items:center; gap:20px;">
                <label style="display:flex; align-items:center; gap:6px; font-size:13px; color:#1a2535; cursor:pointer;">
                    <input type="checkbox" x-model="settleForm.check_received" style="accent-color:#C9A84C;"> Check Received
                </label>
                <label style="display:flex; align-items:center; gap:6px; font-size:13px; color:#7c3aed; font-weight:500; cursor:pointer;">
                    <input type="checkbox" x-model="settleForm.is_policy_limit" style="accent-color:#7c3aed;"> Policy Limit &rarr; Move to UIM
                </label>
            </div>
        </div>
        <div class="sp-modal-footer">
            <button @click="showSettleDemandModal = false" class="sp-btn">Cancel</button>
            <button @click="settleDemand()" class="sp-new-btn-navy">Settle Case</button>
        </div>
    </div>
</div>
