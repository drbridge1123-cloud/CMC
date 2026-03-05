<!-- Edit Commission Modal -->
<div x-show="showEditModal" x-cloak class="sp-modal-overlay" @click.self="showEditModal = false">
    <div class="sp-modal-box" @click.stop>

        <!-- Header -->
        <div class="sp-modal-header">
            <h3 class="sp-modal-title" x-text="editForm._readOnly ? 'Commission Detail' : 'Edit Commission'"></h3>
            <button @click="showEditModal = false" class="sp-modal-close">&times;</button>
        </div>

        <!-- Body -->
        <div class="sp-modal-body">

            <!-- Case Info -->
            <div class="sp-form-grid-2">
                <div>
                    <label class="sp-form-label">Case Number</label>
                    <template x-if="!editForm._readOnly">
                        <input type="text" x-model="editForm.case_number" class="sp-search" style="width:100%;">
                    </template>
                    <template x-if="editForm._readOnly">
                        <div style="padding:8px 12px; background:#fafaf8; border:1px solid #e8e4dc; border-radius:8px; font-size:13px; font-weight:600; color:#1a2535;" x-text="editForm.case_number"></div>
                    </template>
                </div>
                <div>
                    <label class="sp-form-label">Client Name</label>
                    <template x-if="!editForm._readOnly">
                        <input type="text" x-model="editForm.client_name" class="sp-search" style="width:100%;">
                    </template>
                    <template x-if="editForm._readOnly">
                        <div style="padding:8px 12px; background:#fafaf8; border:1px solid #e8e4dc; border-radius:8px; font-size:13px; color:#1a2535;" x-text="editForm.client_name"></div>
                    </template>
                </div>
            </div>

            <!-- Employee + Commission Rate -->
            <div style="display:flex; align-items:center; gap:12px;">
                <template x-if="editForm._employeeName">
                    <div>
                        <label class="sp-form-label">Employee</label>
                        <div style="padding:5px 10px; background:#f0eee8; border-radius:6px; font-size:12px; color:#1a2535; font-weight:500;" x-text="editForm._employeeName"></div>
                    </div>
                </template>
                <div style="margin-left:auto;">
                    <label class="sp-form-label">Commission Rate</label>
                    <div style="padding:5px 10px; border-radius:6px; font-size:12px; font-weight:700;"
                         :style="editForm.is_marketing ? 'background:rgba(201,168,76,.1); color:#C9A84C' : 'background:rgba(26,158,106,.08); color:#1a9e6a'"
                         x-text="(editForm.is_marketing ? '5' : editForm._commissionRate) + '%' + (editForm.is_marketing ? ' (Marketing)' : '')"></div>
                </div>
            </div>

            <!-- ===== SETTLEMENT CALCULATION ===== -->
            <div style="padding:16px; background:#fafaf8; border:1px solid #e8e4dc; border-radius:10px;">
                <div style="font-size:9.5px; font-weight:700; color:#8a8a82; text-transform:uppercase; letter-spacing:.08em; margin-bottom:12px;">Settlement Calculation</div>

                <!-- Row 1: Settled + Pre-Suit Offer -->
                <div class="sp-form-grid-2">
                    <div>
                        <label class="sp-form-label">Settled ($)</label>
                        <template x-if="!editForm._readOnly">
                            <input type="number" x-model.number="editForm.settled" step="0.01" min="0" class="sp-search" style="width:100%;">
                        </template>
                        <template x-if="editForm._readOnly">
                            <div style="padding:8px 12px; background:#fff; border:1px solid #e8e4dc; border-radius:8px; font-family:'IBM Plex Mono',monospace; font-size:13px; color:#1a2535;" x-text="'$' + fmt(editForm.settled)"></div>
                        </template>
                    </div>
                    <div>
                        <label class="sp-form-label">Pre-Suit Offer ($)</label>
                        <template x-if="!editForm._readOnly">
                            <input type="number" x-model.number="editForm.presuit_offer" step="0.01" min="0" class="sp-search" style="width:100%;">
                        </template>
                        <template x-if="editForm._readOnly">
                            <div style="padding:8px 12px; background:#fff; border:1px solid #e8e4dc; border-radius:8px; font-family:'IBM Plex Mono',monospace; font-size:13px; color:#8a8a82;" x-text="'$' + fmt(editForm.presuit_offer)"></div>
                        </template>
                    </div>
                </div>

                <!-- Row 2: Fee Rate + Difference -->
                <div class="sp-form-grid-2" style="margin-top:12px;">
                    <div>
                        <label class="sp-form-label">Fee Rate</label>
                        <template x-if="!editForm._readOnly">
                            <select x-model="editForm.fee_rate" class="sp-select" style="width:100%;">
                                <option value="33.33">33.33%</option><option value="40">40%</option>
                            </select>
                        </template>
                        <template x-if="editForm._readOnly">
                            <div style="padding:8px 12px; background:#fff; border:1px solid #e8e4dc; border-radius:8px; font-family:'IBM Plex Mono',monospace; font-size:13px; color:#1a2535;" x-text="editForm.fee_rate + '%'"></div>
                        </template>
                    </div>
                    <div>
                        <label class="sp-form-label">Difference</label>
                        <div style="padding:8px 12px; background:#fff; border:1px solid #e8e4dc; border-radius:8px; font-family:'IBM Plex Mono',monospace; font-size:13px; color:#1a2535;" x-text="'$' + calcEditDifference()"></div>
                    </div>
                </div>

                <!-- Calculation flow arrow -->
                <div style="text-align:center; color:#c4c0b6; font-size:11px; margin:8px 0;">&#9660;</div>

                <!-- Row 3: Legal Fee → Disc. Legal Fee → Commission -->
                <div class="sp-form-grid-3">
                    <div>
                        <label class="sp-form-label">Legal Fee</label>
                        <div style="padding:8px 10px; background:#fff; border:1px solid #e8e4dc; border-radius:8px; font-family:'IBM Plex Mono',monospace; font-size:12px; color:#8a8a82;" x-text="'$' + calcEditLegalFee()"></div>
                    </div>
                    <div>
                        <label class="sp-form-label">Disc. Legal Fee</label>
                        <div style="padding:8px 10px; background:#fff; border:1px solid #e8e4dc; border-radius:8px; font-family:'IBM Plex Mono',monospace; font-size:12px; color:#1a2535; font-weight:500;" x-text="'$' + calcEditDiscLF()"></div>
                    </div>
                    <div>
                        <label style="display:block; font-size:9.5px; font-weight:700; color:#C9A84C; text-transform:uppercase; letter-spacing:.08em; margin-bottom:5px;">Commission</label>
                        <div style="padding:8px 10px; background:rgba(26,158,106,.06); border:1px solid rgba(26,158,106,.2); border-radius:8px; font-family:'IBM Plex Mono',monospace; font-size:12px; font-weight:700; color:#1a9e6a;" x-text="'$' + calcEditCommission()"></div>
                    </div>
                </div>

                <!-- Formula description -->
                <div style="margin-top:8px; font-size:10px; color:#a8a49c; font-style:italic;" x-text="'(Settled - Pre-Suit) × ' + (editForm.fee_rate || 33.33) + '% = Disc. LF → × ' + (editForm.is_marketing ? '5' : editForm._commissionRate) + '% = Commission'"></div>
            </div>

            <!-- Options + Management -->
            <template x-if="!editForm._readOnly">
                <div style="display:flex; flex-direction:column; gap:16px;">
                    <!-- Options -->
                    <div style="display:flex; align-items:center; gap:20px;">
                        <label style="display:flex; align-items:center; gap:6px; font-size:13px; color:#1a2535; cursor:pointer;">
                            <input type="checkbox" x-model="editForm.is_marketing" style="accent-color:#C9A84C;"> Marketing (5%)
                        </label>
                        <label style="display:flex; align-items:center; gap:6px; font-size:13px; color:#1a2535; cursor:pointer;">
                            <input type="checkbox" x-model="editForm.check_received" style="accent-color:#C9A84C;"> Check Received
                        </label>
                    </div>

                    <div class="sp-form-grid-2">
                        <div>
                            <label class="sp-form-label">Month</label>
                            <input type="text" x-model="editForm.month" placeholder="e.g. Feb. 2026" class="sp-search" style="width:100%;">
                        </div>
                        <template x-if="isAdmin">
                            <div>
                                <label class="sp-form-label">Status</label>
                                <select x-model="editForm.status" class="sp-select" style="width:100%;">
                                    <option value="in_progress">In Progress</option><option value="unpaid">Unpaid</option><option value="paid">Paid</option><option value="rejected">Rejected</option>
                                </select>
                            </div>
                        </template>
                    </div>

                    <div>
                        <label class="sp-form-label">Note</label>
                        <textarea x-model="editForm.note" rows="2" class="sp-search" style="width:100%; resize:none;"></textarea>
                    </div>
                </div>
            </template>

            <!-- Read-only management fields -->
            <template x-if="editForm._readOnly">
                <div style="display:flex; flex-direction:column; gap:12px;">
                    <div class="sp-form-grid-3">
                        <div>
                            <label class="sp-form-label">Month</label>
                            <div style="padding:8px 12px; background:#fafaf8; border:1px solid #e8e4dc; border-radius:8px; font-size:13px; color:#1a2535;" x-text="editForm.month || '—'"></div>
                        </div>
                        <div>
                            <label class="sp-form-label">Status</label>
                            <span class="sp-status" :class="editForm.status === 'paid' ? 'sp-status-paid' : 'sp-status-rejected'" x-text="(editForm.status || '').toUpperCase()"></span>
                        </div>
                        <div>
                            <label class="sp-form-label">Check</label>
                            <span :class="editForm.check_received ? 'ec-check-received' : 'ec-check-pending'" x-text="editForm.check_received ? 'Received' : 'Pending'"></span>
                        </div>
                    </div>
                    <template x-if="editForm.note">
                        <div>
                            <label class="sp-form-label">Note</label>
                            <div style="padding:8px 12px; background:#fafaf8; border:1px solid #e8e4dc; border-radius:8px; font-size:13px; color:#1a2535;" x-text="editForm.note"></div>
                        </div>
                    </template>
                </div>
            </template>

        </div>

        <!-- Footer -->
        <div class="sp-modal-footer">
            <button @click="showEditModal = false" class="sp-btn" x-text="editForm._readOnly ? 'Close' : 'Cancel'"></button>
            <template x-if="!editForm._readOnly">
                <button @click="updateCommission()" :disabled="saving" class="sp-new-btn-navy">
                    <span x-text="saving ? 'Saving...' : 'Save Changes'"></span>
                </button>
            </template>
        </div>

    </div>
</div>
