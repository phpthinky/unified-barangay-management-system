@extends('layouts.barangay')

@section('title', 'Edit Template - ' . $documentType->name)

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2><i class="fas fa-file-code"></i> Template Editor</h2>
            <p class="text-muted mb-0">{{ $documentType->name }}</p>
        </div>
        <div>
            <a href="{{ route('barangay.document-types.show', $documentType) }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back
            </a>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">
        <i class="fas fa-check-circle"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <form action="{{ route('barangay.document-types.template.update', $documentType) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="row">
            <!-- Template Editor -->
            <div class="col-md-8">
                <div class="card mb-4">
                    <div class="card-header bg-warning text-dark">
                        <h5 class="mb-0"><i class="fas fa-code"></i> Template HTML</h5>
                    </div>
                    <div class="card-body">
                       
                       <div class="mb-3">
    <label for="template_content" class="form-label">Template Content</label>
    
    <!-- Quill Editor -->
    <div id="editor-container"></div>
    
    <!-- Hidden textarea for form submission -->
    <textarea class="form-control d-none @error('template_content') is-invalid @enderror" 
              id="template_content" name="template_content">{{ old('template_content', $documentType->template_content) }}</textarea>
    
    @error('template_content')
        <div class="invalid-feedback d-block">{{ $message }}</div>
    @enderror
</div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Save Template
                            </button>
                            <button type="button" class="btn btn-secondary" id="load-sample-template">
                                <i class="fas fa-file-import"></i> Load Sample
                            </button>
                            @if($documentType->template_content)
                            <button type="button" class="btn btn-danger" id="clear-template">
                                <i class="fas fa-trash"></i> Clear & Use Default
                            </button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-md-4">
                <!-- Available Variables -->
                <div class="card mb-4 sticky-top" style="top: 20px;">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0"><i class="fas fa-tags"></i> Available Variables</h5>
                    </div>
                    <div class="card-body">
                        <h6 class="text-primary">Basic Information</h6>
                        <div class="mb-3" style="font-size: 13px;">
                            <code>[NAME]</code> - Full name<br>
                            <code>[ADDRESS]</code> - Complete address<br>
                            <code>[BIRTHDAY]</code> - Date of birth<br>
                            <code>[AGE]</code> - Age in years<br>
                            <code>[SEX]</code> - Gender<br>
                            <code>[CIVIL_STATUS]</code> - Marital status<br>
                            <code>[PLACE_OF_BIRTH]</code> - Birthplace<br>
                            <code>[BARANGAY]</code> - Barangay name<br>
                            <code>[DATE]</code> - Current date
                        </div>

                        @php
                            $formFields = $documentType->form_fields;
                            if (!is_array($formFields)) {
                                $formFields = [];
                            }
                        @endphp

                        @if(count($formFields) > 0)
                        <h6 class="text-success">Custom Form Fields</h6>
                        <div class="mb-3" style="font-size: 13px;">
                            @foreach($formFields as $field)
                                <code>[{{ strtoupper($field['name']) }}]</code> - {{ $field['label'] }}<br>
                            @endforeach
                        </div>
                        @endif

                        <div class="alert alert-warning mt-3 mb-0">
                            <small><i class="fas fa-lightbulb"></i> <strong>Tip:</strong> Use square brackets with UPPERCASE: <code>[VARIABLE]</code></small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@push('styles')
<!-- Quill CSS -->
<link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
<style>
    .ql-editor {
        min-height: 500px;
        font-family: 'Times New Roman', Times, serif;
        font-size: 14px;
    }
    .ql-container {
        font-size: 14px;
    }
</style>
@endpush

@push('scripts')
<!-- Quill JS -->
<script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>

<script>
// Initialize Quill
var quill = new Quill('#editor-container', {
    theme: 'snow',
    modules: {
        toolbar: [
            [{ 'header': [1, 2, 3, 4, 5, 6, false] }],
            ['bold', 'italic', 'underline', 'strike'],
            [{ 'align': [] }],
            [{ 'list': 'ordered'}, { 'list': 'bullet' }],
            [{ 'indent': '-1'}, { 'indent': '+1' }],
            ['link', 'image'],
            ['clean'],
            ['code-block']
        ]
    }
});

// Load existing content
var existingContent = @json(old('template_content', $documentType->template_content));
if (existingContent) {
    quill.clipboard.dangerouslyPasteHTML(existingContent);
}

// Update hidden textarea on form submit
document.querySelector('form').addEventListener('submit', function() {
    document.getElementById('template_content').value = quill.root.innerHTML;
});

// Insert variable buttons
function insertVariable(variable) {
    const cursorPosition = quill.getSelection()?.index || quill.getLength();
    quill.insertText(cursorPosition, variable);
    quill.setSelection(cursorPosition + variable.length);
}

// Load Sample Template
document.getElementById('load-sample-template')?.addEventListener('click', function() {
    if (quill.getText().trim() && !confirm('This will replace the current template. Continue?')) {
        return;
    }
    
    const sampleTemplate = `<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        @page { 
            size: A4;
            margin: 0;
        }
        body { 
            font-family: 'Times New Roman', Times, serif;
            padding: 50px 60px;
            line-height: 1.6;
        }
        .header { 
            text-align: center; 
            margin-bottom: 40px;
            border-bottom: 3px double #000;
            padding-bottom: 20px;
        }
        .header h3 { margin: 5px 0; font-size: 16px; }
        .header h2 { margin: 10px 0; font-size: 20px; font-weight: bold; }
        .title { 
            text-align: center; 
            font-size: 28px; 
            font-weight: bold; 
            margin: 30px 0;
            text-decoration: underline;
        }
        .content { 
            text-align: justify;
            font-size: 14px;
            margin: 30px 0;
        }
        .content p { margin: 15px 0; }
        .signature-section {
            margin-top: 60px;
            text-align: right;
            padding-right: 80px;
        }
        .signature-line { 
            border-top: 2px solid #000; 
            width: 250px; 
            margin: 40px 0 0 auto;
            padding-top: 5px;
            text-align: center;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="header">
        <h3>Republic of the Philippines</h3>
        <h3>Province of Occidental Mindoro</h3>
        <h3>Municipality of Sablayan</h3>
        <h2>BARANGAY [BARANGAY]</h2>
        <p style="font-style: italic; margin-top: 10px;">Office of the Punong Barangay</p>
    </div>

    <div class="title">
        BARANGAY CLEARANCE
    </div>

    <div class="content">
        <p><strong>TO WHOM IT MAY CONCERN:</strong></p>
        
        <p style="text-indent: 50px;">
            This is to certify that <strong>[NAME]</strong>, 
            <strong>[AGE]</strong> years old, <strong>[CIVIL_STATUS]</strong>, 
            a resident of <strong>[ADDRESS]</strong>, Barangay [BARANGAY], 
            Sablayan, Occidental Mindoro, is personally known to me to be of good 
            moral character and a law-abiding citizen of this barangay.
        </p>

        <p style="text-indent: 50px;">
            This further certifies that he/she has no pending criminal case filed 
            in this barangay and has no derogatory record on file.
        </p>

        <p style="text-indent: 50px;">
            This certification is issued upon the request of the above-named person 
            for <strong>[PURPOSE]</strong> purposes and whatever legal purposes 
            it may serve.
        </p>

        <p style="text-indent: 50px;">
            Issued this <strong>[DATE]</strong> at Barangay [BARANGAY], 
            Sablayan, Occidental Mindoro, Philippines.
        </p>
    </div>

    <div class="signature-section">
        <p>Certified by:</p>
        <div class="signature-line">
            [BARANGAY_CAPTAIN]<br>
            PUNONG BARANGAY
        </div>
    </div>
</body>
</html>`;
    
    quill.clipboard.dangerouslyPasteHTML(sampleTemplate);
    alert('Sample template loaded! Remember to customize it for your barangay.');
});

// Clear Template
document.getElementById('clear-template')?.addEventListener('click', function() {
    if (confirm('This will remove the custom template and use the default template. Continue?')) {
        quill.setText('');
    }
});
</script>
@endpush
@endsection