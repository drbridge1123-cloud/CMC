<!-- Move to UIM Modal -->
<div x-show="showToUimModal" x-cloak class="sp-modal-overlay" @click.self="showToUimModal = false">
    <div class="sp-modal-box" @click.stop>
        <div class="sp-modal-header">
            <h3 class="sp-modal-title">Move to UIM</h3>
            <div style="font-size:12px; color:#C9A84C; margin-top:4px;">
                <span x-text="settlingCase?.case_number" style="font-weight:600;"></span> — <span x-text="settlingCase?.client_name"></span>
            </div>
        </div>
        <div class="sp-modal-body">
            <div>
                <label class="sp-form-label">Note</label>
                <textarea x-model="toUimForm.note" rows="2" placeholder="Optional note..." class="sp-search" style="width:100%; resize:none;"></textarea>
            </div>
        </div>
        <div class="sp-modal-footer">
            <button @click="showToUimModal = false" class="sp-btn">Cancel</button>
            <button @click="toUim()" class="sp-new-btn-navy">Move to UIM</button>
        </div>
    </div>
</div>
