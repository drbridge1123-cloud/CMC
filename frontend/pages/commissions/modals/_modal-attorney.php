<!-- Attorney Commission Detail Modal -->
<div x-show="showAttorneyModal" x-cloak class="sp-modal-overlay" @click.self="showAttorneyModal = false">
    <div class="sp-modal-box" @click.stop>

        <!-- Header -->
        <div class="sp-modal-header">
            <h3 class="sp-modal-title">Attorney Commission Detail</h3>
            <button @click="showAttorneyModal = false" class="sp-modal-close">&times;</button>
        </div>

        <!-- Body -->
        <div class="sp-modal-body">

            <!-- Case Info (read-only) -->
            <div class="sp-form-grid-2">
                <div>
                    <label class="sp-form-label">Case Number</label>
                    <div style="padding:8px 12px; background:#fafaf8; border:1px solid #e8e4dc; border-radius:8px; font-size:13px; font-weight:600; color:#1a2535;" x-text="attForm.case_number"></div>
                </div>
                <div>
                    <label class="sp-form-label">Client Name</label>
                    <div style="padding:8px 12px; background:#fafaf8; border:1px solid #e8e4dc; border-radius:8px; font-size:13px; color:#1a2535;" x-text="attForm.client_name"></div>
                </div>
            </div>

            <!-- Phase + Resolution Type -->
            <div style="display:flex; align-items:center; gap:12px;">
                <div>
                    <label class="sp-form-label">Phase</label>
                    <span class="sp-phase" :class="attForm._phaseClass" x-text="attForm._phaseLabel" style="display:inline-block;"></span>
                </div>
                <template x-if="attForm.resolution_type">
                    <div>
                        <label class="sp-form-label">Resolution Type</label>
                        <div style="padding:5px 10px; background:#f0eee8; border-radius:6px; font-size:12px; color:#1a2535; font-weight:500;" x-text="attForm.resolution_type"></div>
                    </div>
                </template>
                <div style="margin-left:auto;">
                    <label class="sp-form-label">Commission Rate</label>
                    <div style="padding:5px 10px; background:rgba(26,158,106,.08); border-radius:6px; font-size:12px; color:#1a9e6a; font-weight:700;" x-text="attForm._commRate + '%'"></div>
                </div>
            </div>

            <!-- ===== SETTLEMENT CALCULATION ===== -->
            <div style="padding:16px; background:#fafaf8; border:1px solid #e8e4dc; border-radius:10px;">
                <div style="font-size:9.5px; font-weight:700; color:#8a8a82; text-transform:uppercase; letter-spacing:.08em; margin-bottom:12px;">Settlement Calculation</div>

                <!-- Row 1: Settled + Pre-Suit Offer (if litigation 33%) -->
                <div class="sp-form-grid-2" :style="attForm._showPresuit ? 'grid-template-columns:1fr 1fr' : 'grid-template-columns:1fr 1fr'">
                    <div>
                        <label class="sp-form-label">Settled ($)</label>
                        <input type="number" x-model.number="attForm.settled" @input="recalcAttComm()" step="0.01" min="0" class="sp-search" style="width:100%;">
                    </div>
                    <template x-if="attForm._showPresuit">
                        <div>
                            <label class="sp-form-label">Pre-Suit Offer ($)</label>
                            <div style="padding:8px 12px; background:#fff; border:1px solid #e8e4dc; border-radius:8px; font-family:'IBM Plex Mono',monospace; font-size:13px; color:#1a2535;" x-text="'$' + fmt(attForm.presuit_offer)"></div>
                        </div>
                    </template>
                    <template x-if="!attForm._showPresuit">
                        <div>
                            <label class="sp-form-label">Fee Rate</label>
                            <div style="padding:8px 12px; background:#fff; border:1px solid #e8e4dc; border-radius:8px; font-family:'IBM Plex Mono',monospace; font-size:13px; color:#1a2535;" x-text="attForm.fee_rate + '%'"></div>
                        </div>
                    </template>
                </div>

                <!-- Row 2: Difference + Fee Rate (only for presuit cases) -->
                <template x-if="attForm._showPresuit">
                    <div class="sp-form-grid-2" style="margin-top:12px;">
                        <div>
                            <label class="sp-form-label">Difference</label>
                            <div style="padding:8px 12px; background:#fff; border:1px solid #e8e4dc; border-radius:8px; font-family:'IBM Plex Mono',monospace; font-size:13px; color:#1a2535;" x-text="'$' + fmt(attCalcDifference())"></div>
                        </div>
                        <div>
                            <label class="sp-form-label">Fee Rate</label>
                            <div style="padding:8px 12px; background:#fff; border:1px solid #e8e4dc; border-radius:8px; font-family:'IBM Plex Mono',monospace; font-size:13px; color:#1a2535;" x-text="attForm.fee_rate + '%'"></div>
                        </div>
                    </div>
                </template>

                <!-- Calculation flow arrow -->
                <div style="text-align:center; color:#c4c0b6; font-size:11px; margin:8px 0;">&#9660;</div>

                <!-- Row 3: Legal Fee → Disc. Legal Fee → Commission -->
                <div class="sp-form-grid-3">
                    <div>
                        <label class="sp-form-label">Legal Fee</label>
                        <div style="padding:8px 10px; background:#fff; border:1px solid #e8e4dc; border-radius:8px; font-family:'IBM Plex Mono',monospace; font-size:12px; color:#1a2535;" x-text="'$' + fmt(attCalcLegalFee())"></div>
                    </div>
                    <div>
                        <label class="sp-form-label">Disc. Legal Fee</label>
                        <input type="number" x-model.number="attForm.discounted_legal_fee" @input="recalcAttCommFromDLF()" step="0.01" min="0" class="sp-search" style="width:100%; font-size:12px;">
                    </div>
                    <div>
                        <label style="display:block; font-size:9.5px; font-weight:700; color:#C9A84C; text-transform:uppercase; letter-spacing:.08em; margin-bottom:5px;">Commission</label>
                        <div style="padding:8px 10px; background:rgba(26,158,106,.06); border:1px solid rgba(26,158,106,.2); border-radius:8px; font-family:'IBM Plex Mono',monospace; font-size:12px; font-weight:700; color:#1a9e6a;" x-text="'$' + fmt(attForm.commission)"></div>
                    </div>
                </div>

                <!-- Formula description -->
                <div style="margin-top:8px; font-size:10px; color:#a8a49c; font-style:italic;" x-text="attForm._formulaDesc"></div>
            </div>

            <!-- ===== UIM SECTION ===== -->
            <template x-if="attForm._hasUim">
                <div style="padding:16px; background:rgba(99,102,241,.03); border:1px solid rgba(99,102,241,.15); border-radius:10px;">
                    <div style="font-size:9.5px; font-weight:700; color:#6366f1; text-transform:uppercase; letter-spacing:.08em; margin-bottom:12px;">UIM Settlement (5%)</div>

                    <div class="sp-form-grid-2">
                        <div>
                            <label class="sp-form-label">UIM Settled ($)</label>
                            <input type="number" x-model.number="attForm.uim_settled" @input="recalcUimComm()" step="0.01" min="0" class="sp-search" style="width:100%;">
                        </div>
                        <div>
                            <label class="sp-form-label">UIM Legal Fee</label>
                            <div style="padding:8px 12px; background:#fff; border:1px solid rgba(99,102,241,.12); border-radius:8px; font-family:'IBM Plex Mono',monospace; font-size:13px; color:#1a2535;" x-text="'$' + fmt(attCalcUimLegalFee())"></div>
                        </div>
                    </div>

                    <div class="sp-form-grid-2" style="margin-top:12px;">
                        <div>
                            <label class="sp-form-label">UIM Disc. Legal Fee</label>
                            <input type="number" x-model.number="attForm.uim_discounted_legal_fee" @input="recalcUimCommFromDLF()" step="0.01" min="0" class="sp-search" style="width:100%;">
                        </div>
                        <div>
                            <label style="display:block; font-size:9.5px; font-weight:700; color:#C9A84C; text-transform:uppercase; letter-spacing:.08em; margin-bottom:5px;">UIM Commission</label>
                            <div style="padding:8px 12px; background:rgba(99,102,241,.06); border:1px solid rgba(99,102,241,.15); border-radius:8px; font-family:'IBM Plex Mono',monospace; font-size:13px; font-weight:700; color:#6366f1;" x-text="'$' + fmt(attForm.uim_commission)"></div>
                        </div>
                    </div>

                    <div style="margin-top:8px; font-size:10px; color:#a8a49c; font-style:italic;">UIM Disc. Legal Fee × 5% = UIM Commission</div>
                </div>
            </template>

            <!-- Total Commission -->
            <div style="padding:14px 16px; background:rgba(26,158,106,.06); border:1px solid rgba(26,158,106,.2); border-radius:10px; display:flex; align-items:center; justify-content:space-between;">
                <span style="font-size:9.5px; font-weight:700; color:rgba(26,158,106,.7); text-transform:uppercase; letter-spacing:.08em;">Total Commission</span>
                <span style="font-size:20px; font-weight:700; font-family:'IBM Plex Mono',monospace; color:#1a9e6a;" x-text="'$' + fmt((attForm.commission || 0) + (attForm.uim_commission || 0))"></span>
            </div>

            <!-- Management Fields -->
            <div class="sp-form-grid-2">
                <div>
                    <label class="sp-form-label">Month</label>
                    <input type="text" x-model="attForm.month" placeholder="e.g. Feb. 2026" class="sp-search" style="width:100%;">
                </div>
                <div>
                    <label class="sp-form-label">Status</label>
                    <select x-model="attForm.status" class="sp-select" style="width:100%;">
                        <option value="unpaid">Unpaid</option>
                        <option value="paid">Paid</option>
                    </select>
                </div>
            </div>

            <div style="display:flex; align-items:center; gap:20px;">
                <label style="display:flex; align-items:center; gap:6px; font-size:13px; color:#1a2535; cursor:pointer;">
                    <input type="checkbox" x-model="attForm.check_received" style="accent-color:#C9A84C;"> Check Received
                </label>
            </div>

            <div>
                <label class="sp-form-label">Note</label>
                <textarea x-model="attForm.note" rows="2" class="sp-search" style="width:100%; resize:none;"></textarea>
            </div>

        </div>

        <!-- Footer -->
        <div class="sp-modal-footer">
            <button @click="showAttorneyModal = false" class="sp-btn">Cancel</button>
            <button @click="saveAttorneyCase()" :disabled="saving" class="sp-new-btn-navy">
                <span x-text="saving ? 'Saving...' : 'Save Changes'"></span>
            </button>
        </div>

    </div>
</div>
