<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>TracAdemics Notification</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f8f9fa;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        .header {
            background: linear-gradient(135deg, #007bff, #0056b3);
            color: white;
            padding: 30px 20px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 600;
        }
        .header .subtitle {
            opacity: 0.9;
            margin-top: 5px;
            font-size: 14px;
        }
        .content {
            padding: 30px 20px;
        }
        .status-badge {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .status-submitted {
            background-color: #fff3cd;
            color: #856404;
        }
        .status-approved {
            background-color: #d1edff;
            color: #0c5460;
        }
        .status-rejected {
            background-color: #f8d7da;
            color: #721c24;
        }
        .info-box {
            background-color: #f8f9fa;
            border-left: 4px solid #007bff;
            padding: 15px;
            margin: 20px 0;
            border-radius: 0 5px 5px 0;
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
        }
        .info-label {
            font-weight: 600;
            color: #666;
        }
        .info-value {
            color: #333;
        }
        .action-button {
            display: inline-block;
            background: linear-gradient(135deg, #007bff, #0056b3);
            color: white;
            padding: 12px 25px;
            text-decoration: none;
            border-radius: 25px;
            font-weight: 600;
            text-align: center;
            margin: 20px 0;
            transition: transform 0.2s;
        }
        .action-button:hover {
            transform: translateY(-2px);
            text-decoration: none;
            color: white;
        }
        .footer {
            background-color: #f8f9fa;
            padding: 20px;
            text-align: center;
            border-top: 1px solid #dee2e6;
            font-size: 12px;
            color: #666;
        }
        .comments-box {
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 5px;
            padding: 15px;
            margin: 15px 0;
        }
        .comments-title {
            font-weight: 600;
            color: #495057;
            margin-bottom: 8px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div style="display: flex; align-items: center; justify-content: center; margin-bottom: 10px;">
                <img src="{{ asset('images/tracademics-logo-small.svg') }}" alt="TracAdemics" style="width: 40px; height: 40px; margin-right: 15px;">
                <h1 style="margin: 0;">TracAdemics</h1>
            </div>
            <div class="subtitle">Brokenshire University Academic Compliance System</div>
        </div>
        
        <div class="content">
            @if($type === 'submitted')
                <h2>Document Submitted Successfully</h2>
                <p>Hello {{ $submission->user->name }},</p>
                <p>Your document has been successfully submitted and is now pending review.</p>
            @elseif($type === 'approved')
                <h2>Document Approved ✅</h2>
                <p>Hello {{ $submission->user->name }},</p>
                <p>Congratulations! Your document submission has been approved.</p>
            @elseif($type === 'rejected')
                <h2>Document Requires Attention ⚠️</h2>
                <p>Hello {{ $submission->user->name }},</p>
                <p>Your document submission requires some modifications before it can be approved.</p>
            @endif
            
            <div class="info-box">
                <div class="info-row">
                    <span class="info-label">Document Type:</span>
                    <span class="info-value">{{ $submission->documentType->name }}</span>
                </div>
                @if($submission->subject)
                <div class="info-row">
                    <span class="info-label">Subject:</span>
                    <span class="info-value">{{ $submission->subject->code }} - {{ $submission->subject->name }}</span>
                </div>
                @endif
                <div class="info-row">
                    <span class="info-label">Status:</span>
                    <span class="status-badge status-{{ $submission->status }}">{{ ucfirst($submission->status) }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Submitted:</span>
                    <span class="info-value">{{ $submission->submitted_at->format('M d, Y g:i A') }}</span>
                </div>
                @if($submission->reviewed_at)
                <div class="info-row">
                    <span class="info-label">Reviewed:</span>
                    <span class="info-value">{{ $submission->reviewed_at->format('M d, Y g:i A') }}</span>
                </div>
                @endif
            </div>
            
            @if($submission->review_comments && ($type === 'approved' || $type === 'rejected'))
                <div class="comments-box">
                    <div class="comments-title">Review Comments:</div>
                    <div>{{ $submission->review_comments }}</div>
                </div>
            @endif
            
            @if($type === 'submitted')
                <p>Your submission is now in the review queue. You will receive another notification once it has been reviewed.</p>
                <a href="{{ route('compliance.my-submissions') }}" class="action-button">View My Submissions</a>
            @elseif($type === 'approved')
                <p>No further action is required for this submission. Thank you for your compliance!</p>
                <a href="{{ route('compliance.my-submissions') }}" class="action-button">View My Submissions</a>
            @elseif($type === 'rejected')
                <p>Please review the comments above and resubmit your document with the necessary corrections.</p>
                <a href="{{ route('compliance.my-submissions') }}" class="action-button">View & Resubmit</a>
            @endif
            
            <p>If you have any questions, please contact your department administrator or the MIS team.</p>
        </div>
        
        <div class="footer">
            <p>This is an automated message from TracAdemics - Brokenshire University<br>
            Please do not reply to this email.</p>
            <p>© {{ date('Y') }} Brokenshire University. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
