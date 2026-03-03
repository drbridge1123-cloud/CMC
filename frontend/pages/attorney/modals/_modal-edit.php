<!-- Edit Attorney Case Modal -->
<div x-show="showEditModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center" style="background:rgba(0,0,0,.45);" @click.self="showEditModal = false">
    <div style="background:#fff; border-radius:12px; box-shadow:0 24px 64px rgba(0,0,0,.22); width:100%; max-width:560px; max-height:90vh; overflow:hidden; display:flex; flex-direction:column;" @click.stop>
        <div style="background:#0F1B2D; padding:18px 24px; display:flex; align-items:center; justify-content:space-between; flex-shrink:0;">
            <h3 style="font-size:15px; font-weight:700; color:#fff; margin:0;">Edit Attorney Case</h3>
            <button @click="showEditModal = false" style="background:none; border:none; color:rgba(255,255,255,.4); cursor:pointer; font-size:20px;">&times;</button>
        </div>
        <div style="padding:24px; overflow-y:auto; display:flex; flex-direction:column; gap:16px;">
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px;">
                <div>
                    <label style="display:block; font-size:9.5px; font-weight:700; color:#8a8a82; text-transform:uppercase; letter-spacing:.08em; margin-bottom:5px;">Case Number</label>
                    <input type="text" x-model="editForm.case_number" class="sp-search" style="width:100%;">
                </div>
                <div>
                    <label style="display:block; font-size:9.5px; font-weight:700; color:#8a8a82; text-transform:uppercase; letter-spacing:.08em; margin-bottom:5px;">Client Name</label>
                    <input type="text" x-model="editForm.client_name" class="sp-search" style="width:100%;">
                </div>
            </div>
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px;">
                <div>
                    <label style="display:block; font-size:9.5px; font-weight:700; color:#8a8a82; text-transform:uppercase; letter-spacing:.08em; margin-bottom:5px;">Case Type</label>
                    <select x-model="editForm.case_type" class="sp-select" style="width:100%;">
                        <option value="Auto">Auto</option><option value="Pedestrian">Pedestrian</option><option value="Motorcycle">Motorcycle</option><option value="Slip & Fall">Slip & Fall</option><option value="Dog Bite">Dog Bite</option><option value="Other">Other</option>
                    </select>
                </div>
                <div>
                    <label style="display:block; font-size:9.5px; font-weight:700; color:#8a8a82; text-transform:uppercase; letter-spacing:.08em; margin-bottom:5px;">Assigned Date</label>
                    <input type="date" x-model="editForm.assigned_date" class="sp-search" style="width:100%;">
                </div>
            </div>
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px;">
                <div>
                    <label style="display:block; font-size:9.5px; font-weight:700; color:#8a8a82; text-transform:uppercase; letter-spacing:.08em; margin-bottom:5px;">Stage</label>
                    <select x-model="editForm.stage" class="sp-select" style="width:100%;">
                        <option value="">Select...</option>
                        <option value="demand_review">Demand Review</option><option value="demand_write">Demand Write</option><option value="demand_sent">Demand Sent</option><option value="negotiate">Negotiate</option>
                    </select>
                </div>
                <div>
                    <label style="display:block; font-size:9.5px; font-weight:700; color:#8a8a82; text-transform:uppercase; letter-spacing:.08em; margin-bottom:5px;">Month</label>
                    <input type="text" x-model="editForm.month" placeholder="e.g. Feb. 2025" class="sp-search" style="width:100%;">
                </div>
            </div>
            <!-- Date Override -->
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px; padding:12px; border-radius:8px; background:#fff8e6; border:1px dashed #C9A84C;">
                <div>
                    <label style="display:block; font-size:9.5px; font-weight:700; color:#8a8a82; text-transform:uppercase; letter-spacing:.08em; margin-bottom:5px;">Demand Out Date</label>
                    <input type="date" x-model="editForm.demand_out_date" class="sp-search" style="width:100%;">
                </div>
                <div>
                    <label style="display:block; font-size:9.5px; font-weight:700; color:#8a8a82; text-transform:uppercase; letter-spacing:.08em; margin-bottom:5px;">Negotiate Start Date</label>
                    <input type="date" x-model="editForm.negotiate_date" class="sp-search" style="width:100%;">
                </div>
                <div>
                    <label style="display:block; font-size:9.5px; font-weight:700; color:#8a8a82; text-transform:uppercase; letter-spacing:.08em; margin-bottom:5px;">Demand Deadline</label>
                    <input type="date" x-model="editForm.demand_deadline" class="sp-search" style="width:100%;">
                </div>
                <div>
                    <label style="display:block; font-size:9.5px; font-weight:700; color:#8a8a82; text-transform:uppercase; letter-spacing:.08em; margin-bottom:5px;">Top Offer Date</label>
                    <input type="date" x-model="editForm.top_offer_date" class="sp-search" style="width:100%;">
                </div>
            </div>
            <div>
                <label style="display:block; font-size:9.5px; font-weight:700; color:#8a8a82; text-transform:uppercase; letter-spacing:.08em; margin-bottom:5px;">Note</label>
                <textarea x-model="editForm.note" rows="2" class="sp-search" style="width:100%; resize:none;"></textarea>
            </div>
            <div style="display:flex; align-items:center; gap:20px;">
                <label style="display:flex; align-items:center; gap:6px; font-size:13px; color:#1a2535; cursor:pointer;">
                    <input type="checkbox" x-model="editForm.check_received" style="accent-color:#C9A84C;"> Check Received
                </label>
                <label style="display:flex; align-items:center; gap:6px; font-size:13px; color:#1a2535; cursor:pointer;">
                    <input type="checkbox" x-model="editForm.is_marketing" style="accent-color:#C9A84C;"> Marketing Case
                </label>
            </div>

            <!-- Transfer History -->
            <div style="border-top:1px solid #e8e4dc; padding-top:14px;">
                <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:8px;">
                    <span style="font-size:9.5px; font-weight:700; color:#8a8a82; text-transform:uppercase; letter-spacing:.08em;">Transfer History</span>
                    <button type="button" @click="openTransferFromEdit()" class="sp-act" title="Transfer this case"
                            style="width:auto; padding:0 10px; font-size:10px; height:22px; color:#0F1B2D; border-color:rgba(15,27,45,.25); background:rgba(15,27,45,.04);">
                        Transfer &#8594;
                    </button>
                </div>
                <!-- Current attorney -->
                <div style="display:flex; align-items:flex-start; gap:8px; padding:6px 0;">
                    <div style="width:6px; height:6px; border-radius:50%; background:#1a9e6a; margin-top:5px; flex-shrink:0;"></div>
                    <div style="font-size:12px; line-height:1.4;">
                        <span style="font-weight:600; color:#1a2535;" x-text="editForm._attorney_name"></span>
                        <span style="color:#8a8a82;">— current</span>
                        <template x-if="transferHistory.length > 0">
                            <span style="color:#8a8a82; font-size:11px;"
                                  x-text="'(since ' + new Date(transferHistory[0].transferred_at).toLocaleDateString('en-US', {month:'short', day:'numeric', year:'numeric'}) + ')'"></span>
                        </template>
                        <template x-if="transferHistory.length === 0 && editForm.assigned_date">
                            <span style="color:#8a8a82; font-size:11px;"
                                  x-text="'(since ' + new Date(editForm.assigned_date + 'T00:00:00').toLocaleDateString('en-US', {month:'short', day:'numeric', year:'numeric'}) + ')'"></span>
                        </template>
                    </div>
                </div>
                <!-- History entries -->
                <template x-for="t in transferHistory" :key="t.id">
                    <div style="display:flex; align-items:flex-start; gap:8px; padding:6px 0; border-top:1px solid #f5f2ee;">
                        <div style="width:6px; height:6px; border-radius:50%; background:#d0cdc5; margin-top:5px; flex-shrink:0;"></div>
                        <div style="font-size:12px; line-height:1.4;">
                            <span style="font-weight:600; color:#1a2535;" x-text="t.from_name"></span>
                            <span style="color:#8a8a82; font-size:11px;"
                                  x-text="t.from_start_date
                                      ? new Date(t.from_start_date + 'T00:00:00').toLocaleDateString('en-US', {month:'short', day:'numeric'})
                                        + ' – ' + new Date(t.transferred_at).toLocaleDateString('en-US', {month:'short', day:'numeric', year:'numeric'})
                                      : '– ' + new Date(t.transferred_at).toLocaleDateString('en-US', {month:'short', day:'numeric', year:'numeric'})"></span>
                            <span style="color:#8a8a82;">&#8594;</span>
                            <span style="font-weight:600; color:#1a2535;" x-text="t.to_name"></span>
                            <div x-show="t.note" style="color:#5A6B82; font-size:11px; font-style:italic; margin-top:2px;" x-text="t.note"></div>
                        </div>
                    </div>
                </template>
                <div x-show="transferHistory.length === 0" style="font-size:11px; color:#b0a89c; padding:4px 0;">No transfers yet</div>
            </div>
        </div>
        <div style="padding:14px 24px; border-top:1px solid #e8e4dc; display:flex; justify-content:flex-end; gap:10px; flex-shrink:0;">
            <button @click="showEditModal = false" class="sp-btn">Cancel</button>
            <button @click="updateCase()" class="sp-new-btn-navy">Save Changes</button>
        </div>
    </div>
</div>
