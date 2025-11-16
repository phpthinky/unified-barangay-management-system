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

                        <div class="alert alert-info mb-3">
                            <strong><i class="fas fa-info-circle"></i> Document Editor Tips (Works like MS Word):</strong>
                            <ul class="mb-0 mt-2">
                                <li><strong>Spacebar:</strong> Works normally - multiple spaces are preserved</li>
                                <li><strong>Paragraph Indent:</strong> Click the indent button <i class="fas fa-indent"></i> for 50px text indent (formal style)</li>
                                <li><strong>Enter Key:</strong> Creates new paragraph (press Enter once or twice for spacing)</li>
                                <li><strong>Alignment:</strong> Default is justified (like formal documents), use toolbar to change</li>
                                <li><strong>Font:</strong> Times New Roman 12pt (standard document format)</li>
                                <li><strong>Variables:</strong> Type in UPPERCASE with brackets: [NAME], [ADDRESS], [DATE]</li>
                            </ul>
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
    /* Word-like document editor styling */
    .ql-editor {
        min-height: 600px;
        font-family: 'Times New Roman', Times, serif;
        font-size: 12pt;
        line-height: 1.5;
        padding: 1in;
        background: white;
        border: 1px solid #ddd;
        box-shadow: 0 0 5px rgba(0,0,0,0.1);
        white-space: pre-wrap !important;  /* Preserve spaces like Word */
        word-wrap: break-word;
    }

    .ql-container {
        font-size: 12pt;
        border: none !important;
    }

    /* Word-like paragraph behavior */
    .ql-editor p {
        margin: 0;
        line-height: 1.5;
        text-align: justify;  /* Default justify like formal documents */
        text-indent: 0;
    }

    /* Add spacing between paragraphs (like pressing Enter in Word) */
    .ql-editor p + p {
        margin-top: 0;
    }

    /* Indented paragraphs (50px text-indent) */
    .ql-editor blockquote {
        border-left: none;
        padding-left: 0;
        margin: 0;
        text-indent: 50px;
        text-align: justify;
    }

    /* Alternative: Use indent-1 for text-indent */
    .ql-editor .ql-indent-1 {
        text-indent: 50px;
        padding-left: 0;
    }

    .ql-editor .ql-indent-2 {
        text-indent: 100px;
        padding-left: 0;
    }

    /* Alignment overrides */
    .ql-editor .ql-align-center {
        text-align: center;
        text-indent: 0;
    }

    .ql-editor .ql-align-right {
        text-align: right;
        text-indent: 0;
    }

    .ql-editor .ql-align-left {
        text-align: left;
        text-indent: 0;
    }

    .ql-editor .ql-align-justify {
        text-align: justify;
    }

    /* Preserve multiple spaces */
    .ql-editor * {
        white-space: pre-wrap !important;
    }

    /* Line breaks should create actual space */
    .ql-editor br {
        display: block;
        content: "";
        line-height: 1.5;
    }

    /* Bold, italic, underline preserve spacing */
    .ql-editor strong,
    .ql-editor em,
    .ql-editor u {
        white-space: pre-wrap;
    }

    /* Paper-like appearance */
    #editor-container {
        background: #f5f5f5;
        padding: 20px;
        border-radius: 4px;
    }
</style>
@endpush

@push('scripts')
<!-- Quill JS -->
<script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>

<script>
// Initialize Quill with enhanced formatting options
var quill = new Quill('#editor-container', {
    theme: 'snow',
    modules: {
        toolbar: [
            [{ 'header': [1, 2, 3, 4, 5, 6, false] }],
            [{ 'size': ['small', false, 'large', 'huge'] }],  // Font size
            ['bold', 'italic', 'underline', 'strike'],
            [{ 'script': 'sub'}, { 'script': 'super' }],      // Subscript/Superscript
            [{ 'color': [] }, { 'background': [] }],          // Text color and background
            [{ 'align': [] }],
            [{ 'list': 'ordered'}, { 'list': 'bullet' }],
            [{ 'indent': '-1'}, { 'indent': '+1' }],
            ['blockquote'],                                    // Blockquote for indented text
            ['link'],
            ['clean']
        ]
    }
});

// Load existing content
var existingContent = @json(old('template_content', $documentType->template_content));
if (existingContent) {
    quill.clipboard.dangerouslyPasteHTML(existingContent);
}

// Update hidden textarea whenever Quill content changes
quill.on('text-change', function(delta, oldDelta, source) {
    document.getElementById('template_content').value = quill.root.innerHTML;
});

// Update hidden textarea on form submit
document.querySelector('form').addEventListener('submit', function(e) {
    // Get the HTML content from Quill editor
    var htmlContent = quill.root.innerHTML;

    // Update the hidden textarea
    document.getElementById('template_content').value = htmlContent;

    // Debug: Log to console to verify content is being captured
    console.log('Quill content being saved:', htmlContent.substring(0, 100) + '...');
});

// Insert variable buttons
function insertVariable(variable) {
    const cursorPosition = quill.getSelection()?.index || quill.getLength();
    quill.insertText(cursorPosition, variable);
    quill.setSelection(cursorPosition + variable.length);
}

// Load Sample Template (Quill-formatted version)
document.getElementById('load-sample-template')?.addEventListener('click', function() {
    if (quill.getText().trim() && !confirm('This will replace the current template. Continue?')) {
        return;
    }

    // Word-like formatted template with proper indentation
    const sampleTemplate = `<p class="ql-align-center"><strong>Republic of the Philippines</strong></p>
<p class="ql-align-center"><strong>Province of Occidental Mindoro</strong></p>
<p class="ql-align-center"><strong>Municipality of Sablayan</strong></p>
<p class="ql-align-center"><strong style="font-size: 18px;">BARANGAY [BARANGAY]</strong></p>
<p class="ql-align-center"><em>Office of the Punong Barangay</em></p>
<p><br></p>
<p class="ql-align-center"><strong><u style="font-size: 20px;">BARANGAY CLEARANCE</u></strong></p>
<p><br></p>
<p><strong>TO WHOM IT MAY CONCERN:</strong></p>
<p><br></p>
<p class="ql-indent-1">This is to certify that <strong>[NAME]</strong>, <strong>[AGE]</strong> years old, <strong>[CIVIL_STATUS]</strong>, a resident of <strong>[ADDRESS]</strong>, Barangay [BARANGAY], Sablayan, Occidental Mindoro, is personally known to me to be of good moral character and a law-abiding citizen of this barangay.</p>
<p><br></p>
<p class="ql-indent-1">This further certifies that he/she has no pending criminal case filed in this barangay and has no derogatory record on file.</p>
<p><br></p>
<p class="ql-indent-1">This certification is issued upon the request of the above-named person for <strong>[PURPOSE]</strong> purposes and whatever legal purposes it may serve.</p>
<p><br></p>
<p class="ql-indent-1">Issued this <strong>[DATE]</strong> at Barangay [BARANGAY], Sablayan, Occidental Mindoro, Philippines.</p>
<p><br></p>
<p><br></p>
<p class="ql-align-right">Certified by:</p>
<p><br></p>
<p><br></p>
<p><br></p>
<p class="ql-align-right">_______________________________</p>
<p class="ql-align-right"><strong>[BARANGAY_CAPTAIN]</strong></p>
<p class="ql-align-right">Punong Barangay</p>`;

    /* OLD COMPLEX HTML TEMPLATE - keeping for reference
    const oldSampleTemplate = `<!DOCTYPE html>
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
    */

    quill.clipboard.dangerouslyPasteHTML(sampleTemplate);
    alert('Sample template loaded!\n\nThe editor works like MS Word:\n• Spacebar creates actual spaces\n• Use the INDENT button for 50px paragraph indents\n• Text is justified by default\n• Customize for your barangay!');
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