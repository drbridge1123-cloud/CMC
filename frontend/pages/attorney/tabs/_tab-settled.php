<!-- Settled Table -->
<div style="overflow-x:auto;">
    <table class="sp-table sp-table-compact">
        <thead>
            <tr>
                <th>Case #</th>
                <th>Client</th>
                <th class="center">Phase</th>
                <th class="right">Settled</th>
                <th class="right">UIM Settled</th>
                <th class="center">Month</th>
                <th class="center">Check</th>
                <th class="center">Status</th>
                <th class="right">Duration</th>
                <th>Attorney</th>
                <th class="center">Action</th>
            </tr>
        </thead>
        <tbody>
            <template x-if="loading">
                <tr><td colspan="11" class="sp-loading">Loading settled cases...</td></tr>
            </template>
            <template x-if="!loading && filteredSettledCases.length === 0">
                <tr><td colspan="11" class="sp-empty">No settled cases found</td></tr>
            </template>
            <template x-for="c in paginatedSettledCases" :key="c.id">
                <tr @click="openEdit(c)">

                    <!-- Case # -->
                    <td><span class="sp-case-num" x-text="c.case_number"></span></td>

                    <!-- Client -->
                    <td><span class="sp-client" x-text="c.client_name"></span></td>

                    <!-- Phase -->
                    <td style="text-align:center">
                        <span class="sp-phase" :class="settledPhaseClass(c)"
                              x-text="settledPhaseLabel(c)"></span>
                    </td>

                    <!-- Settled -->
                    <td style="text-align:right">
                        <span class="sp-mono" x-text="formatCurrency(c.settled)"></span>
                    </td>

                    <!-- UIM Settled -->
                    <td style="text-align:right">
                        <template x-if="c.uim_settled">
                            <span class="sp-mono" x-text="formatCurrency(c.uim_settled)"></span>
                        </template>
                        <template x-if="!c.uim_settled">
                            <span class="sp-dash">—</span>
                        </template>
                    </td>

                    <!-- Month -->
                    <td style="text-align:center">
                        <span class="sp-month" x-text="c.month || '—'"></span>
                    </td>

                    <!-- Check -->
                    <td style="text-align:center" @click.stop>
                        <button class="sp-check" :class="c.check_received == 1 && 'checked'"
                                @click="toggleCheck(c)"
                                x-text="c.check_received == 1 ? '✓' : ''"></button>
                    </td>

                    <!-- Status -->
                    <td style="text-align:center">
                        <template x-if="c.status === 'billing_review'">
                            <span class="sp-status sp-status-in-progress" style="background:rgba(201,168,76,.08); color:#8a6d1b; border-color:rgba(201,168,76,.25);">Billing Review</span>
                        </template>
                        <template x-if="c.status === 'accounting'">
                            <span class="sp-status sp-status-in-progress">In Accounting</span>
                        </template>
                        <template x-if="c.status === 'closed'">
                            <span class="sp-status sp-status-paid">Closed</span>
                        </template>
                        <template x-if="c.status !== 'billing_review' && c.status !== 'accounting' && c.status !== 'closed'">
                            <span class="sp-status" :class="(c.status === 'paid') ? 'sp-status-paid' : 'sp-status-unpaid'"
                                  x-text="c.status || 'unpaid'"></span>
                        </template>
                    </td>

                    <!-- Duration -->
                    <td style="text-align:right">
                        <span class="sp-duration-num" style="font-size:12px; font-weight:600" x-text="settledDuration(c)"></span><span class="sp-duration-unit">d</span>
                    </td>

                    <!-- Attorney -->
                    <td>
                        <span class="sp-month" x-text="c.attorney_name || '—'"></span>
                    </td>

                    <!-- Action -->
                    <td style="text-align:center" @click.stop>
                        <!-- Fresh settled: show both Billing and Acct buttons -->
                        <template x-if="c.status !== 'billing_review' && c.status !== 'accounting' && c.status !== 'closed'">
                            <div style="display:flex; gap:4px; justify-content:center;">
                                <button @click="openSendBillingModal(c)" class="sp-act" title="Send to Billing Review"
                                        style="width:auto; padding:0 8px; font-size:10px; height:22px; color:#8a6d1b; border-color:rgba(201,168,76,.4); background:rgba(201,168,76,.06);">
                                    Billing &#8594;
                                </button>
                                <button @click="openSendAcctModal(c)" class="sp-act sp-act-blue" title="Send to Accounting (bypass billing)"
                                        style="width:auto; padding:0 8px; font-size:10px; height:22px;">
                                    Acct &#8594;
                                </button>
                            </div>
                        </template>
                        <!-- In billing review: show label + Acct button -->
                        <template x-if="c.status === 'billing_review'">
                            <div style="display:flex; gap:4px; align-items:center; justify-content:center;">
                                <span style="font-size:10px; color:#8a6d1b;">&#9679; Billing</span>
                                <button @click="openSendAcctModal(c)" class="sp-act sp-act-blue" title="Send to Accounting"
                                        style="width:auto; padding:0 8px; font-size:10px; height:22px;">
                                    Acct &#8594;
                                </button>
                            </div>
                        </template>
                        <!-- In accounting -->
                        <template x-if="c.status === 'accounting'">
                            <span style="font-size:10px; color:#2563eb;">&#9679; Acct</span>
                        </template>
                        <!-- Closed -->
                        <template x-if="c.status === 'closed'">
                            <span style="font-size:10px; color:#1a9e6a;">&#10003;</span>
                        </template>
                    </td>

                </tr>
            </template>
        </tbody>
    </table>
</div>
