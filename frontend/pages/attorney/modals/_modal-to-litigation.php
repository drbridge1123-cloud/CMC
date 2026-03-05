<!-- Move to Litigation Modal -->
<div x-show="showToLitModal" x-cloak class="sp-modal-overlay" @click.self="showToLitModal = false">
    <div class="sp-modal-box" @click.stop>
        <div class="sp-modal-header">
            <h3 class="sp-modal-title">Move to Litigation</h3>
            <div style="font-size:12px; color:#C9A84C; margin-top:4px;">
                <span x-text="settlingCase?.case_number" style="font-weight:600;"></span> — <span x-text="settlingCase?.client_name"></span>
            </div>
        </div>
        <div class="sp-modal-body">
            <div class="sp-form-grid-2">
                <div>
                    <label class="sp-form-label">Litigation Start Date</label>
                    <input type="date" x-model="toLitForm.litigation_start_date" class="sp-search" style="width:100%;">
                </div>
                <div>
                    <label class="sp-form-label">Pre-Suit Offer ($)</label>
                    <input type="number" x-model.number="toLitForm.presuit_offer" step="0.01" min="0" class="sp-search" style="width:100%;">
                </div>
            </div>
            <div>
                <label class="sp-form-label">Note</label>
                <textarea x-model="toLitForm.note" rows="2" class="sp-search" style="width:100%; resize:none;"></textarea>
            </div>
        </div>
        <div class="sp-modal-footer">
            <button @click="showToLitModal = false" class="sp-btn">Cancel</button>
            <button @click="toLitigation()" class="sp-new-btn-navy">Move to Litigation</button>
        </div>
    </div>
</div>
