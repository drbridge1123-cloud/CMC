<!-- Create Commission Modal -->
<div x-show="showCreateModal" x-cloak class="sp-modal-overlay" @click.self="showCreateModal = false">
    <div class="sp-modal-box" @click.stop>
        <div class="sp-modal-header">
            <h3 class="sp-modal-title">Add Commission</h3>
            <button @click="showCreateModal = false" class="sp-modal-close">&times;</button>
        </div>
        <div class="sp-modal-body">
            <div class="sp-form-grid-2">
                <div>
                    <label class="sp-form-label">Case Number *</label>
                    <input type="text" x-model="createForm.case_number" required class="sp-search" style="width:100%;">
                </div>
                <div>
                    <label class="sp-form-label">Client Name *</label>
                    <input type="text" x-model="createForm.client_name" required class="sp-search" style="width:100%;">
                </div>
            </div>
            <div class="sp-form-grid-2">
                <div>
                    <label class="sp-form-label">Case Type</label>
                    <select x-model="createForm.case_type" class="sp-select" style="width:100%;">
                        <option value="Auto">Auto</option><option value="Slip & Fall">Slip & Fall</option><option value="Other">Other</option>
                    </select>
                </div>
                <div x-show="isAdmin">
                    <label class="sp-form-label">Employee</label>
                    <select x-model="createForm.employee_user_id" class="sp-select" style="width:100%;">
                        <option value="">-- Select --</option>
                        <template x-for="u in employees" :key="u.id"><option :value="u.id" x-text="u.display_name || u.full_name"></option></template>
                    </select>
                </div>
            </div>
            <!-- Settlement Section -->
            <div style="padding-top:16px; border-top:1px solid #e8e4dc;">
                <div style="font-size:9.5px; font-weight:700; color:#C9A84C; text-transform:uppercase; letter-spacing:.08em; margin-bottom:12px;">Settlement Details</div>
                <div class="sp-form-grid-2">
                    <div>
                        <label class="sp-form-label">Settled ($)</label>
                        <input type="number" x-model.number="createForm.settled" step="0.01" min="0" class="sp-search" style="width:100%;">
                    </div>
                    <div>
                        <label class="sp-form-label">Pre-Suit Offer ($)</label>
                        <input type="number" x-model.number="createForm.presuit_offer" step="0.01" min="0" class="sp-search" style="width:100%;">
                    </div>
                </div>
                <div class="sp-form-grid-2" style="margin-top:12px;">
                    <div>
                        <label class="sp-form-label">Fee Rate (%)</label>
                        <select x-model="createForm.fee_rate" class="sp-select" style="width:100%;">
                            <option value="33.33">33.33%</option><option value="40">40%</option>
                        </select>
                    </div>
                    <div>
                        <label class="sp-form-label">Month</label>
                        <input type="text" x-model="createForm.month" placeholder="e.g. Feb. 2025" class="sp-search" style="width:100%;">
                    </div>
                </div>
            </div>
            <!-- Options -->
            <div style="display:flex; align-items:center; gap:20px;">
                <label style="display:flex; align-items:center; gap:6px; font-size:13px; color:#1a2535; cursor:pointer;">
                    <input type="checkbox" x-model="createForm.is_marketing" style="accent-color:#C9A84C;"> Marketing (5%)
                </label>
                <label style="display:flex; align-items:center; gap:6px; font-size:13px; color:#1a2535; cursor:pointer;">
                    <input type="checkbox" x-model="createForm.check_received" style="accent-color:#C9A84C;"> Check Received
                </label>
            </div>
            <div>
                <label class="sp-form-label">Note</label>
                <textarea x-model="createForm.note" rows="2" class="sp-search" style="width:100%; resize:none;"></textarea>
            </div>
        </div>
        <div class="sp-modal-footer">
            <button @click="showCreateModal = false" class="sp-btn">Cancel</button>
            <button @click="createCommission()" :disabled="saving" class="sp-new-btn-navy">
                <span x-text="saving ? 'Saving...' : 'Add Commission'"></span>
            </button>
        </div>
    </div>
</div>
