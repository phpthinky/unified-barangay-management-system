// public/js/complaint-workflow.js or at bottom of show.blade.php

function showSecretaryReviewModal() {
    const modal = new bootstrap.Modal(document.getElementById('secretaryReviewModal'));
    modal.show();
}

function showDecisionModal(decision) {
    const modal = new bootstrap.Modal(document.getElementById('captainDecisionModal'));
    const isApprove = decision.toLowerCase().includes('approve');
    
    document.getElementById('decisionInput').value = isApprove ? 'approve' : 'dismiss';
    document.getElementById('decisionModalTitle').textContent = decision;
    
    const submitBtn = document.getElementById('decisionSubmitBtn');
    submitBtn.textContent = decision;
    submitBtn.className = 'btn ' + (isApprove ? 'btn-success' : 'btn-danger');
    
    modal.show();
}

function showSummonsModal() {
    const modal = new bootstrap.Modal(document.getElementById('summonsModal'));
    modal.show();
}

function showAppearanceModal() {
    const modal = new bootstrap.Modal(document.getElementById('appearanceModal'));
    modal.show();
}

function showSettlementModal() {
    const modal = new bootstrap.Modal(document.getElementById('settlementModal'));
    modal.show();
}

function showLuponModal() {
    const modal = new bootstrap.Modal(document.getElementById('luponModal'));
    modal.show();
}

function showCertificateModal() {
    const modal = new bootstrap.Modal(document.getElementById('certificateModal'));
    modal.show();
}