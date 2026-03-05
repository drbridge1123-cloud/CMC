<!-- Top Offer Modal -->
<div x-show="showTopOfferModal" x-cloak class="sp-modal-overlay" @click.self="showTopOfferModal = false">
    <div class="sp-modal-box" @click.stop>
        <div class="sp-modal-header">
            <h3 class="sp-modal-title">Submit Top Offer</h3>
            <div style="font-size:12px; color:#C9A84C; margin-top:4px;">
                <span x-text="settlingCase?.case_number" style="font-weight:600;"></span> — <span x-text="settlingCase?.client_name"></span>
            </div>
        </div>
        <div class="sp-modal-body">
            <div class="sp-form-grid-2">
                <div>
                    <label class="sp-form-label">Top Offer Amount ($) *</label>
                    <input type="number" x-model.number="topOfferForm.top_offer_amount" step="0.01" min="0" required class="sp-search" style="width:100%;">
                </div>
                <div>
                    <label class="sp-form-label">Assign To</label>
                    <select x-model="topOfferForm.assignee_id" class="sp-select" style="width:100%;">
                        <option value="">Select...</option>
                        <template x-for="u in users" :key="u.id"><option :value="u.id" x-text="u.name"></option></template>
                    </select>
                </div>
            </div>
            <div>
                <label class="sp-form-label">Note</label>
                <textarea x-model="topOfferForm.note" rows="3" placeholder="Optional note..." class="sp-search" style="width:100%; resize:none;"></textarea>
            </div>
        </div>
        <div class="sp-modal-footer">
            <button @click="showTopOfferModal = false" class="sp-btn">Cancel</button>
            <button @click="submitTopOffer()" class="sp-new-btn-navy">Submit Top Offer</button>
        </div>
    </div>
</div>
