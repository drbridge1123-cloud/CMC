<!-- Settle Litigation Modal -->
<div x-show="showSettleLitModal" x-cloak class="sp-modal-overlay" @click.self="showSettleLitModal = false">
    <div class="sp-modal-box" @click.stop>
        <div class="sp-modal-header">
            <h3 class="sp-modal-title">Settle Litigation Case</h3>
            <div style="font-size:12px; color:#C9A84C; margin-top:4px;">
                <span x-text="settlingCase?.case_number" style="font-weight:600;"></span> — <span x-text="settlingCase?.client_name"></span>
                <span style="margin-left:8px; color:rgba(255,255,255,.5);">Pre-Suit Offer: <span style="color:#fff; font-weight:600;" x-text="'$' + (settlingCase?.presuit_offer || 0).toLocaleString()"></span></span>
            </div>
        </div>
        <div class="sp-modal-body">
            <!-- Commission mode: full form -->
            <template x-if="hasCommission">
                <div style="display:flex; flex-direction:column; gap:16px;">
                    <div class="sp-form-grid-2">
                        <div>
                            <label class="sp-form-label">Resolution Type *</label>
                            <select x-model="settleLitForm.resolution_type" class="sp-select" style="width:100%;">
                                <option value="">-- Select --</option>
                                <optgroup label="33.33% (Pre-Suit Deducted)">
                                    <option value="No Offer Settle">No Offer Settle</option>
                                    <option value="File and Bump">File and Bump</option>
                                    <option value="Post Deposition Settle">Post Deposition Settle</option>
                                    <option value="Mediation">Mediation</option>
                                    <option value="Settled Post Arbitration">Settled Post Arbitration</option>
                                    <option value="Settlement Conference">Settlement Conference</option>
                                </optgroup>
                                <optgroup label="40% (No Deduction)">
                                    <option value="Arbitration Award">Arbitration Award</option>
                                    <option value="Beasley">Beasley</option>
                                </optgroup>
                                <optgroup label="Variable (Manual)">
                                    <option value="Co-Counsel">Co-Counsel</option>
                                    <option value="Other">Other</option>
                                </optgroup>
                            </select>
                        </div>
                        <div>
                            <label class="sp-form-label">Settled Amount ($) *</label>
                            <input type="number" x-model.number="settleLitForm.settled" step="0.01" min="0" required class="sp-search" style="width:100%;">
                        </div>
                    </div>
                    <div class="sp-form-grid-3" x-show="settleLitForm.resolution_type">
                        <div>
                            <label class="sp-form-label">Difference</label>
                            <div style="padding:8px 12px; background:#fafaf8; border:1px solid #e8e4dc; border-radius:8px; font-size:13px; font-family:'IBM Plex Mono',monospace; color:#1a2535;"
                                 x-text="'$' + getLitDifference().toLocaleString(undefined, {minimumFractionDigits: 2})"></div>
                        </div>
                        <div>
                            <label class="sp-form-label">Fee Rate</label>
                            <div style="padding:8px 12px; background:#fafaf8; border:1px solid #e8e4dc; border-radius:8px; font-size:13px; font-family:'IBM Plex Mono',monospace; font-weight:600; color:#1a2535;"
                                 x-text="getLitFeeRate(settleLitForm.resolution_type) != null ? getLitFeeRate(settleLitForm.resolution_type) + '%' : 'Manual'"></div>
                        </div>
                        <div>
                            <label class="sp-form-label">Comm. Rate</label>
                            <div style="padding:8px 12px; background:#fafaf8; border:1px solid #e8e4dc; border-radius:8px; font-size:13px; font-family:'IBM Plex Mono',monospace; font-weight:600; color:#1a2535;"
                                 x-text="getLitCommRate() + '%'"></div>
                        </div>
                    </div>
                    <div class="sp-form-grid-2">
                        <div>
                            <label class="sp-form-label">Disc. Legal Fee ($) *</label>
                            <input type="number" x-model.number="settleLitForm.discounted_legal_fee" step="0.01" min="0" required class="sp-search" style="width:100%;">
                        </div>
                        <div>
                            <label class="sp-form-label">Commission</label>
                            <div style="padding:8px 12px; background:rgba(21,128,61,.06); border:1px solid rgba(21,128,61,.2); border-radius:8px; font-size:13px; font-family:'IBM Plex Mono',monospace; font-weight:700; color:#15803d;"
                                 x-text="'$' + calcLitCommission(settleLitForm.discounted_legal_fee, getLitCommRate()).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})"></div>
                        </div>
                    </div>
                    <div class="sp-form-grid-2" x-show="isVariableType(settleLitForm.resolution_type)">
                        <div>
                            <label class="sp-form-label">Manual Fee Rate (%)</label>
                            <input type="number" x-model.number="settleLitForm.manual_fee_rate" step="0.01" min="0" max="100" class="sp-search" style="width:100%;">
                        </div>
                        <div>
                            <label class="sp-form-label">Manual Commission Rate (%)</label>
                            <input type="number" x-model.number="settleLitForm.manual_commission_rate" step="0.01" min="0" max="100" class="sp-search" style="width:100%;">
                        </div>
                    </div>
                    <div x-show="settleLitForm.resolution_type && !isVariableType(settleLitForm.resolution_type)">
                        <label style="display:flex; align-items:center; gap:6px; font-size:13px; color:#ea580c; font-weight:500; cursor:pointer;">
                            <input type="checkbox" x-model="settleLitForm.fee_rate_override" style="accent-color:#ea580c;"> Override fee rate
                        </label>
                    </div>
                    <div class="sp-form-grid-2">
                        <div>
                            <label class="sp-form-label">Month</label>
                            <input type="text" x-model="settleLitForm.month" placeholder="e.g. Feb. 2026" class="sp-search" style="width:100%;">
                        </div>
                        <div x-show="settleLitForm.fee_rate_override || isVariableType(settleLitForm.resolution_type)">
                            <label class="sp-form-label">Note *</label>
                            <input type="text" x-model="settleLitForm.note" placeholder="Required when overriding" class="sp-search" style="width:100%;">
                        </div>
                    </div>
                </div>
            </template>
            <!-- No commission mode: simple form -->
            <template x-if="!hasCommission">
                <div>
                    <label class="sp-form-label">Settled Amount ($) *</label>
                    <input type="number" x-model.number="settleLitForm.settled" step="0.01" min="0" required class="sp-search" style="width:100%;">
                </div>
            </template>
            <div style="display:flex; align-items:center; gap:20px;">
                <label style="display:flex; align-items:center; gap:6px; font-size:13px; color:#1a2535; cursor:pointer;">
                    <input type="checkbox" x-model="settleLitForm.check_received" style="accent-color:#C9A84C;"> Check Received
                </label>
                <label style="display:flex; align-items:center; gap:6px; font-size:13px; color:#7c3aed; font-weight:500; cursor:pointer;">
                    <input type="checkbox" x-model="settleLitForm.is_policy_limit" style="accent-color:#7c3aed;"> Policy Limit &rarr; UIM
                </label>
            </div>
        </div>
        <div class="sp-modal-footer">
            <button @click="showSettleLitModal = false" class="sp-btn">Cancel</button>
            <button @click="settleLitigation()" class="sp-new-btn-navy">Settle Case</button>
        </div>
    </div>
</div>
