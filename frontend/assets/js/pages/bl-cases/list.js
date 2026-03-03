function casesListPage() {
    return {
        ...listPageBase('bl-cases', {
            defaultSort: 'case_number',
            defaultDir: 'desc',
            perPage: 9999,
            filtersToParams() {
                return {
                    status: this.search ? '' : this.statusFilter,
                    assigned_to: this.assignedFilter,
                };
            }
        }),

        // Page-specific state
        summary: {},
        statusFilter: '',
        assignedFilter: '',
        showCreateModal: false,
        saving: false,
        staffList: [],
        newCase: { case_number: '', client_name: '', client_dob: '', doi: '', attorney_name: '', assigned_to: '', notes: '' },

        _resetPageFilters() {
            this.statusFilter = '';
            this.assignedFilter = '';
        },

        _hasPageFilters() {
            return this.statusFilter || this.assignedFilter;
        },

        async createCase() {
            this.saving = true;
            try {
                await api.post('bl-cases', { ...this.newCase });
                showToast('Case created successfully');
                this.showCreateModal = false;
                this.newCase = { case_number: '', client_name: '', client_dob: '', doi: '', attorney_name: '', assigned_to: '', notes: '' };
                this.loadData(1);
            } catch (e) {
                showToast(e.data?.message || 'Failed to create case', 'error');
            }
            this.saving = false;
        },

        async deleteCase(id, caseNumber, clientName) {
            if (!confirm(`Delete case ${caseNumber} (${clientName})? This will also delete all providers, requests, and notes for this case.`)) return;
            try {
                await api.delete('bl-cases/' + id);
                showToast('Case deleted');
                this.loadData(this.pagination?.page || 1);
            } catch (e) {
                showToast(e.data?.message || 'Failed to delete case', 'error');
            }
        },

        // Reassign modal
        showReassignModal: false,
        reassignForm: { caseId: null, caseNumber: '', assigned_to: '' },

        openReassignModal(c) {
            this.reassignForm = { caseId: c.id, caseNumber: c.case_number, clientName: c.client_name, assigned_to: '' };
            this.showReassignModal = true;
        },

        async submitReassign() {
            if (!this.reassignForm.assigned_to) { showToast('Please select a staff member', 'error'); return; }
            this.saving = true;
            try {
                await api.put('bl-cases/' + this.reassignForm.caseId + '/assign', { assigned_to: parseInt(this.reassignForm.assigned_to) });
                showToast('Case reassigned', 'success');
                this.showReassignModal = false;
                this.loadData(this.pagination?.page || 1);
            } catch(e) {
                showToast(e.data?.message || 'Failed to reassign', 'error');
            }
            this.saving = false;
        },

        caseStatusClass(status) {
            const map = {
                collecting: 'sp-stage-demand-write',
                verification: 'sp-stage-demand-review',
                completed: 'sp-stage-settled',
                rfd: 'sp-stage-demand-sent',
                final_verification: 'sp-stage-demand-review',
                prelitigation: 'sp-stage-litigation',
                accounting: 'sp-stage-mediation',
                disbursement: 'sp-stage-trial-set',
                closed: '',
            };
            return map[status] || '';
        },

        exportCSV() {
            const params = new URLSearchParams();
            if (this.statusFilter) params.set('status', this.statusFilter);
            if (this.assignedFilter) params.set('assigned_to', this.assignedFilter);
            if (this.search) params.set('search', this.search);
            const qs = params.toString();
            window.location.href = '/CMC/backend/api/cases/export' + (qs ? '?' + qs : '');
        },

        // Tracker navigation
        getTrackerLabel(status) {
            const map = {
                prelitigation: 'Prelit',
                collecting: 'Billing',
                verification: 'Billing',
                completed: 'Attorney',
                rfd: 'Attorney',
                final_verification: 'Attorney',
                disbursement: 'Acctg',
                accounting: 'Acctg',
                closed: 'Closed',
            };
            return map[status] || '—';
        },

        goToTracker(c) {
            const caseNum = encodeURIComponent(c.case_number);
            const map = {
                prelitigation:      '/CMC/frontend/pages/prelitigation/index.php',
                collecting:         '/CMC/frontend/pages/billing/index.php?case_id=' + c.id,
                verification:       '/CMC/frontend/pages/billing/index.php?case_id=' + c.id,
                completed:          '/CMC/frontend/pages/attorney/index.php?search=' + caseNum + '&from=case-detail&case_id=' + c.id,
                rfd:                '/CMC/frontend/pages/attorney/index.php?search=' + caseNum + '&from=case-detail&case_id=' + c.id,
                final_verification: '/CMC/frontend/pages/attorney/index.php?search=' + caseNum + '&from=case-detail&case_id=' + c.id,
                disbursement:       '/CMC/frontend/pages/accounting/index.php?search=' + caseNum + '&case_id=' + c.id,
                accounting:         '/CMC/frontend/pages/accounting/index.php?search=' + caseNum + '&case_id=' + c.id,
                closed:             '/CMC/frontend/pages/accounting/index.php?search=' + caseNum + '&case_id=' + c.id,
            };
            const url = map[c.status];
            if (url) window.location.href = url;
        },

        fromAttorneyCases: false,

        async init() {
            try {
                const res = await api.get('users?active_only=1');
                this.staffList = res.data || [];
            } catch (e) {}

            // Check for incoming search from Attorney Cases
            const urlParams = new URLSearchParams(window.location.search);
            const urlSearch = urlParams.get('search');
            if (urlSearch) {
                this.search = urlSearch;
            }
            if (urlParams.get('from') === 'attorney-cases' || urlParams.get('from') === 'attorney') {
                this.fromAttorneyCases = true;
            }

            const auth = Alpine.store('auth');
            if (auth.loading) {
                await new Promise(r => {
                    const iv = setInterval(() => { if (!auth.loading) { clearInterval(iv); r(); } }, 50);
                });
            }

            const uid = auth.user?.id;
            if (uid === 2 && !urlSearch) this.statusFilter = 'collecting';

            await this.loadData();
        }
    };
}
