{{-- Secretary Review Modal with Respondent Verification --}}
<div class="modal fade" id="secretaryReviewModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="{{ route('barangay.complaints-workflow.secretary-review', $complaint) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Secretary Review & Respondent Verification</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    {{-- Respondent Information Section --}}
                    <div class="card mb-3">
                        <div class="card-header bg-light">
                            <h6 class="mb-0">Respondent Information</h6>
                        </div>
                        <div class="card-body">
                            @foreach($complaint->respondents as $index => $respondent)
                            <div class="respondent-item mb-3 p-3 border rounded">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <strong>Respondent {{ $index + 1 }}</strong>
                                    <span class="badge bg-{{ $respondent['type'] === 'named' ? 'info' : 'warning' }}">
                                        {{ ucfirst($respondent['type']) }}
                                    </span>
                                </div>

                                @if($respondent['type'] === 'named')
                                    {{-- Named Respondent - Can Search & Link --}}
                                    <div class="mb-2">
                                        <label class="form-label">Name Provided by Complainant:</label>
                                        <input type="text" class="form-control" value="{{ $respondent['name'] }}" readonly>
                                    </div>

                                    @if(isset($respondent['alias']) && $respondent['alias'])
                                    <div class="mb-2">
                                        <label class="form-label">Alias:</label>
                                        <input type="text" class="form-control" value="{{ $respondent['alias'] }}" readonly>
                                    </div>
                                    @endif

                                    {{-- Search RBI Records --}}
                                    <div class="mb-3">
                                        <label class="form-label">Search & Verify Respondent:</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control" 
                                                   id="search_respondent_{{ $index }}"
                                                   placeholder="Search by name in RBI records...">
                                            <button type="button" class="btn btn-primary" 
                                                    onclick="searchRespondent({{ $index }}, '{{ $respondent['name'] }}')">
                                                <i class="fas fa-search"></i> Search
                                            </button>
                                        </div>
                                        <div id="search_results_{{ $index }}" class="mt-2"></div>
                                    </div>

                                    {{-- Linked User/RBI Record --}}
                                    <input type="hidden" name="respondents[{{ $index }}][linked_user_id]" 
                                           id="linked_user_{{ $index }}" value="">
                                    <input type="hidden" name="respondents[{{ $index }}][linked_rbi_id]" 
                                           id="linked_rbi_{{ $index }}" value="">

                                    {{-- Verified Details --}}
                                    <div class="mb-2">
                                        <label class="form-label">Verified Full Name:</label>
                                        <input type="text" class="form-control" 
                                               name="respondents[{{ $index }}][verified_name]" 
                                               value="{{ $respondent['name'] }}">
                                    </div>

                                    <div class="mb-2">
                                        <label class="form-label">Verified Address:</label>
                                        <textarea class="form-control" rows="2"
                                                  name="respondents[{{ $index }}][verified_address]">{{ $respondent['address'] ?? '' }}</textarea>
                                    </div>

                                    <div class="mb-2">
                                        <label class="form-label">Verified Contact:</label>
                                        <input type="text" class="form-control" 
                                               name="respondents[{{ $index }}][verified_contact]"
                                               value="{{ $respondent['contact'] ?? '' }}">
                                    </div>

                                @else
                                    {{-- Unknown Respondent --}}
                                    <div class="alert alert-warning">
                                        <i class="fas fa-exclamation-triangle"></i> 
                                        <strong>Unknown Suspect</strong> - Identity to be determined
                                    </div>

                                    <div class="mb-2">
                                        <label class="form-label">Description from Complainant:</label>
                                        <textarea class="form-control" rows="3" readonly>{{ $respondent['description'] ?? 'No description provided' }}</textarea>
                                    </div>

                                    {{-- Secretary can try to identify --}}
                                    <div class="mb-2">
                                        <label class="form-label">Identified Name (if found):</label>
                                        <input type="text" class="form-control" 
                                               name="respondents[{{ $index }}][identified_name]"
                                               placeholder="Enter name if identity is confirmed">
                                    </div>

                                    <div class="mb-2">
                                        <label class="form-label">Additional Information:</label>
                                        <textarea class="form-control" rows="2"
                                                  name="respondents[{{ $index }}][additional_info]"
                                                  placeholder="Any additional details discovered"></textarea>
                                    </div>
                                @endif

                                {{-- Verification Status --}}
                                <div class="mb-2">
                                    <label class="form-label">Verification Status:</label>
                                    <select class="form-select" name="respondents[{{ $index }}][verification_status]" required>
                                        <option value="">-- Select --</option>
                                        <option value="verified">Verified - Identity Confirmed</option>
                                        <option value="partial">Partially Verified - Some details unclear</option>
                                        <option value="unverified">Cannot Verify - More info needed</option>
                                        <option value="not_found">Not Found - Cannot locate</option>
                                    </select>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- Secretary Notes --}}
                    <div class="mb-3">
                        <label class="form-label">Secretary Notes for Captain:</label>
                        <textarea name="notes" class="form-control" rows="4" 
                                  placeholder="Add notes about verification, recommendations, or concerns..."></textarea>
                        <small class="text-muted">This will be visible to the Captain during review.</small>
                    </div>

                    {{-- Recommendation --}}
                    <div class="mb-3">
                        <label class="form-label">Recommendation:</label>
                        <select class="form-select" name="recommendation">
                            <option value="proceed">Proceed to Captain Review</option>
                            <option value="needs_info">Request More Information from Complainant</option>
                            <option value="dismiss">Recommend Dismissal</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" 
                            onclick="printComplaintReport()">
                        <i class="fas fa-print"></i> Print for Captain
                    </button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-paper-plane"></i> Submit to Captain
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Search for respondent in RBI records
function searchRespondent(index, defaultName) {
    const searchTerm = document.getElementById(`search_respondent_${index}`).value || defaultName;
    const resultsDiv = document.getElementById(`search_results_${index}`);
    
    resultsDiv.innerHTML = '<div class="text-center"><div class="spinner-border spinner-border-sm"></div> Searching...</div>';
    
    fetch(`/barangay/rbi/search?name=${encodeURIComponent(searchTerm)}`)
        .then(response => response.json())
        .then(data => {
            if (data.results && data.results.length > 0) {
                let html = '<div class="list-group">';
                data.results.forEach(person => {
                    html += `
                        <button type="button" class="list-group-item list-group-item-action" 
                                onclick="selectRespondent(${index}, ${person.id}, '${person.type}', '${person.name}', '${person.address}', '${person.contact}')">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <strong>${person.name}</strong><br>
                                    <small class="text-muted">${person.address}</small>
                                </div>
                                <span class="badge bg-${person.type === 'user' ? 'success' : 'info'}">${person.type === 'user' ? 'Registered' : 'RBI'}</span>
                            </div>
                        </button>
                    `;
                });
                html += '</div>';
                resultsDiv.innerHTML = html;
            } else {
                resultsDiv.innerHTML = '<div class="alert alert-warning">No matching records found</div>';
            }
        })
        .catch(error => {
            resultsDiv.innerHTML = '<div class="alert alert-danger">Search failed</div>';
        });
}

// Select respondent from search results
function selectRespondent(index, id, type, name, address, contact) {
    if (type === 'user') {
        document.getElementById(`linked_user_${index}`).value = id;
    } else {
        document.getElementById(`linked_rbi_${index}`).value = id;
    }
    
    document.querySelector(`[name="respondents[${index}][verified_name]"]`).value = name;
    document.querySelector(`[name="respondents[${index}][verified_address]"]`).value = address;
    document.querySelector(`[name="respondents[${index}][verified_contact]"]`).value = contact;
    document.querySelector(`[name="respondents[${index}][verification_status]"]`).value = 'verified';
    
    document.getElementById(`search_results_${index}`).innerHTML = 
        `<div class="alert alert-success"><i class="fas fa-check"></i> Selected: ${name}</div>`;
}

// Print complaint report for captain
function printComplaintReport() {
    window.open(`/barangay/complaints-workflow/{{ $complaint->id }}/print-report`, '_blank');
}
</script>
@endpush
{{-- Secretary Review Modal - Bootstrap 5.3 
<div class="modal fade" id="secretaryReviewModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('barangay.complaints-workflow.secretary-review', $complaint) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Prepare for Captain Review</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> This will forward the complaint to the Captain for approval/dismissal.
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Notes (Optional)</label>
                        <textarea name="notes" class="form-control" rows="3" 
                                  placeholder="Add any notes for the Captain..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Submit to Captain</button>
                </div>
            </form>
        </div>
    </div>
</div>
--}}
{{-- Captain Decision Modal - Bootstrap 5.3 --}}
<div class="modal fade" id="captainDecisionModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('barangay.complaints-workflow.captain-decision', $complaint) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="decisionModalTitle">Captain Decision</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="decision" id="decisionInput">
                    
                    <div class="mb-3">
                        <label class="form-label">Notes</label>
                        <textarea name="notes" class="form-control" rows="4" placeholder="Reason for approval/dismissal..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn" id="decisionSubmitBtn">Confirm</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Issue Summons Modal - Bootstrap 5.3 --}}
<div class="modal fade" id="summonsModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('barangay.complaints-workflow.issue-summons', $complaint) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Issue Summons #{{ $complaint->summons_attempt + 1 }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Return Date <span class="text-danger">*</span></label>
                        <input type="date" name="return_date" class="form-control" required 
                               min="{{ now()->addDays(3)->format('Y-m-d') }}">
                        <small class="text-muted">Respondent must appear on this date (3-5 days recommended)</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Issue Summons</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Record Appearance Modal - Bootstrap 5.3 --}}
<div class="modal fade" id="appearanceModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('barangay.complaints-workflow.record-appearance', $complaint) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Record Respondent Appearance</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Did respondent appear? <span class="text-danger">*</span></label>
                        <select name="appeared" class="form-select" required>
                            <option value="">-- Select --</option>
                            <option value="1">Yes - Respondent Appeared</option>
                            <option value="0">No - Did Not Appear</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Notes (Optional)</label>
                        <textarea name="notes" class="form-control" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Record Appearance</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Record Settlement Modal - Bootstrap 5.3 --}}
<div class="modal fade" id="settlementModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="{{ route('barangay.complaints-workflow.record-settlement', $complaint) }}" method="POST">
                @csrf
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title">Record Settlement Agreement</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> Document the agreed settlement terms between parties.
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Settlement Terms <span class="text-danger">*</span></label>
                        <textarea name="settlement_terms" class="form-control" rows="6" required 
                                  placeholder="Enter the complete settlement agreement..."></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Additional Notes (Optional)</label>
                        <textarea name="notes" class="form-control" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">Record Settlement</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Assign to Lupon Modal - Bootstrap 5.3 --}}
<div class="modal fade" id="luponModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('barangay.complaints-workflow.assign-lupon', $complaint) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Assign to Lupon</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Select Lupon Member <span class="text-danger">*</span></label>
                        <select name="lupon_id" class="form-select" required>
                            <option value="">-- Select Lupon --</option>
                            @foreach($complaint->barangay->luponMembers as $lupon)
                                <option value="{{ $lupon->id }}">{{ $lupon->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Assignment Notes</label>
                        <textarea name="notes" class="form-control" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Assign to Lupon</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Issue Certificate Modal - Bootstrap 5.3 --}}
<div class="modal fade" id="certificateModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('barangay.complaints-workflow.issue-certificate', $complaint) }}" method="POST">
                @csrf
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">Issue Certificate to File Action</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle"></i> This allows complainant to escalate to court/police.
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Refer To <span class="text-danger">*</span></label>
                        <select name="referred_to" class="form-select" required>
                            <option value="">-- Select --</option>
                            <option value="Police">Police</option>
                            <option value="Court">Court</option>
                            <option value="Prosecutor's Office">Prosecutor's Office</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Notes</label>
                        <textarea name="notes" class="form-control" rows="3" 
                                  placeholder="Reason for certificate issuance..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Issue Certificate</button>
                </div>
            </form>
        </div>
    </div>
</div>