<!-- Create Attorney Case Modal -->
<div x-show="showCreateModal" x-cloak class="sp-modal-overlay" @click.self="showCreateModal = false">
    <div class="sp-modal-box" @click.stop>
        <div class="sp-modal-header">
            <h3 class="sp-modal-title">Add New Attorney Case</h3>
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
                        <option value="Auto">Auto</option><option value="Pedestrian">Pedestrian</option><option value="Motorcycle">Motorcycle</option><option value="Slip & Fall">Slip & Fall</option><option value="Dog Bite">Dog Bite</option><option value="Other">Other</option>
                    </select>
                </div>
                <div>
                    <label class="sp-form-label">Assigned Date</label>
                    <input type="date" x-model="createForm.assigned_date" class="sp-search" style="width:100%;">
                </div>
            </div>
            <div class="sp-form-grid-2">
                <div>
                    <label class="sp-form-label">Phase</label>
                    <select x-model="createForm.phase" class="sp-select" style="width:100%;">
                        <option value="demand">Demand</option><option value="litigation">Litigation</option>
                    </select>
                </div>
                <div>
                    <label class="sp-form-label">Attorney</label>
                    <select x-model="createForm.attorney_user_id" class="sp-select" style="width:100%;">
                        <option value="">Select...</option>
                        <template x-for="u in users" :key="u.id"><option :value="u.id" x-text="u.name"></option></template>
                    </select>
                </div>
            </div>
            <div>
                <label class="sp-form-label">Note</label>
                <textarea x-model="createForm.note" rows="2" class="sp-search" style="width:100%; resize:none;"></textarea>
            </div>
        </div>
        <div class="sp-modal-footer">
            <button @click="showCreateModal = false" class="sp-btn">Cancel</button>
            <button @click="createCase()" class="sp-new-btn-navy">Add Case</button>
        </div>
    </div>
</div>
