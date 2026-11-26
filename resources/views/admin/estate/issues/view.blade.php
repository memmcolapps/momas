@extends('layouts.main')
@section('content')

    <div class="content">
        <div class="container-fluid">

            <!-- Page Title / Breadcrumb -->
            <div class="py-3 d-flex align-items-center justify-content-between">
                <div>
                    <a href="/admin/logged-issues" class="text-decoration-none" style="color: #6B7280; font-size: 13px;">
                        <i data-feather="chevron-left" style="width: 14px; height: 14px; vertical-align: middle;"></i> Logged Issue List
                    </a>
                </div>
            </div>

            <!-- Alerts -->
            @if (isset($errors) && $errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert" style="border-radius: 8px;">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            @if (session()->has('message'))
                <div class="alert alert-success alert-dismissible fade show" role="alert" style="border-radius: 8px;">
                    {{ session()->get('message') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            @if (session()->has('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert" style="border-radius: 8px;">
                    {{ session()->get('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <!-- KPI Widget (Figma Image 2) -->
            <div class="row mb-3">
                <div class="col-auto">
                    <div class="card shadow-none mb-0" style="min-width: 260px; border-radius: 12px; border: 1px solid #E5E7EB; background: #ffffff;">
                        <div class="card-body p-3">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span style="color: #6B7280; font-size: 13px; font-weight: 500;">Total Logged Issue</span>
                                <div style="width: 36px; height: 36px; border-radius: 50%; background-color: #E8F8EE; display: flex; align-items: center; justify-content: center;">
                                    <i data-feather="target" style="width: 18px; height: 18px; color: #1E7E34;"></i>
                                </div>
                            </div>
                            <h3 style="font-size: 28px; font-weight: 700; color: #111827; margin: 0;">{{ $totalLoggedIssue ?? 1 }}</h3>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Detail / Ticket Conversation Card (Figma Images 2, 3, 4, 5) -->
            <div class="row">
                <div class="col-12">
                    <div class="card shadow-none" style="border-radius: 12px; border: 1px solid #E5E7EB; background: #ffffff; overflow: hidden;">
                        
                        <!-- Header with Title and Close Ticket Button -->
                        <div class="card-header bg-white py-3 px-4 d-flex justify-content-between align-items-center" style="border-bottom: 1px solid #F3F4F6;">
                            <h5 class="m-0" style="font-size: 16px; font-weight: 600; color: #111827;">Logged Issue List</h5>
                            @if($issue->status !== \App\Models\LoggedIssue::STATUS_CLOSED)
                                <button type="button" class="btn btn-sm" data-bs-toggle="modal" data-bs-target="#closeTicketModal"
                                        style="background-color: #B91C1C; color: #FFFFFF; font-size: 13px; font-weight: 500; padding: 6px 16px; border-radius: 6px; border: none;">
                                    Close Ticket
                                </button>
                            @endif
                        </div>

                        <div class="card-body p-4">
                            <!-- 4 Customer Info Input Fields (Read-Only) -->
                            <div class="row g-3 mb-4">
                                <div class="col-md-3">
                                    <label class="form-label" style="font-size: 13px; font-weight: 500; color: #374151; margin-bottom: 6px;">First Name</label>
                                    <input type="text" class="form-control" value="{{ $issue->first_name }}" readonly
                                           style="background-color: #FAFAFA; border: 1px solid #E5E7EB; color: #374151; font-size: 14px; border-radius: 6px; padding: 9px 12px;">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label" style="font-size: 13px; font-weight: 500; color: #374151; margin-bottom: 6px;">Last Name</label>
                                    <input type="text" class="form-control" value="{{ $issue->last_name }}" readonly
                                           style="background-color: #FAFAFA; border: 1px solid #E5E7EB; color: #374151; font-size: 14px; border-radius: 6px; padding: 9px 12px;">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label" style="font-size: 13px; font-weight: 500; color: #374151; margin-bottom: 6px;">Phone</label>
                                    <input type="text" class="form-control" value="{{ $issue->customer_phone }}" readonly
                                           style="background-color: #FAFAFA; border: 1px solid #E5E7EB; color: #374151; font-size: 14px; border-radius: 6px; padding: 9px 12px;">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label" style="font-size: 13px; font-weight: 500; color: #374151; margin-bottom: 6px;">Email</label>
                                    <input type="text" class="form-control" value="{{ $issue->customer_email }}" readonly
                                           style="background-color: #FAFAFA; border: 1px solid #E5E7EB; color: #374151; font-size: 14px; border-radius: 6px; padding: 9px 12px;">
                                </div>
                            </div>

                            <hr style="border-top: 1px solid #F3F4F6; margin: 24px 0;">

                            <!-- Conversation Thread -->
                            <div class="conversation-thread mb-4">
                                @forelse($issue->conversation as $msg)
                                    <div class="message-item py-3" style="border-bottom: 1px solid #F3F4F6;">
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <div>
                                                <span style="font-weight: 700; color: #166534; font-size: 14px;">{{ $msg['sender'] }}</span>
                                                <span style="color: #6B7280; font-size: 13px; margin-left: 6px;">#{{ $issue->ticket_id_number }}</span>
                                            </div>
                                            <span style="color: #6B7280; font-size: 12px;">{{ $msg['time'] }}</span>
                                        </div>
                                        <p style="color: #374151; font-size: 14px; margin: 0; line-height: 1.5; white-space: pre-wrap;">{{ $msg['message'] }}</p>
                                        @if(!empty($msg['attachment']))
                                            <div class="mt-2">
                                                <a href="/{{ $msg['attachment'] }}" target="_blank" class="btn btn-sm btn-outline-secondary" style="font-size: 12px; border-radius: 4px; padding: 4px 10px;">
                                                    <i data-feather="paperclip" style="width: 12px; height: 12px;"></i> View Attachment
                                                </a>
                                            </div>
                                        @endif
                                    </div>
                                @empty
                                    <div class="text-center py-4 text-muted fs-14">
                                        No messages in this ticket yet.
                                    </div>
                                @endforelse
                            </div>

                            <!-- Footer: Reply Box or Chat Disabled -->
                            @if($issue->status === \App\Models\LoggedIssue::STATUS_CLOSED)
                                <div class="text-center py-4" style="color: #6B7280; font-style: italic; font-size: 14px;">
                                    *chat disabled*
                                </div>
                            @else
                                <div class="mt-4">
                                    <form action="/admin/reply-issue" method="POST">
                                        @csrf
                                        <input type="hidden" name="id" value="{{ $issue->id }}">
                                        <div class="mb-3">
                                            <textarea name="message" rows="3" class="form-control" placeholder="Type your reply here." required
                                                      style="border: 1px solid #E5E7EB; border-radius: 8px; padding: 12px 14px; font-size: 14px; resize: none;"></textarea>
                                        </div>
                                        <div class="d-flex justify-content-end">
                                            <button type="submit" class="btn"
                                                    style="background-color: #1E7E34; color: #FFFFFF; font-size: 13px; font-weight: 500; padding: 8px 24px; border-radius: 6px; border: none;">
                                                Reply Ticket
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            @endif

                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- Close Ticket Confirmation Modal (Figma Image 3) -->
    <div class="modal fade" id="closeTicketModal" tabindex="-1" aria-labelledby="closeTicketModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" style="max-width: 440px;">
            <div class="modal-content" style="border-radius: 12px; border: none; box-shadow: 0 10px 25px rgba(0,0,0,0.1);">
                <div class="modal-body p-4 text-center">
                    <h5 class="fw-semibold text-dark mb-4" style="font-size: 16px; margin-top: 8px;">
                        Are you sure you want to close this ticket?
                    </h5>

                    <form action="/admin/close-issue" method="POST">
                        @csrf
                        <input type="hidden" name="id" value="{{ $issue->id }}">
                        <div class="d-flex justify-content-center gap-3">
                            <button type="button" class="btn" data-bs-dismiss="modal"
                                    style="background: transparent; border: 1px solid #DC2626; color: #DC2626; font-size: 14px; font-weight: 500; padding: 8px 28px; border-radius: 6px;">
                                Cancel
                            </button>
                            <button type="submit" class="btn"
                                    style="background-color: #B91C1C; border: none; color: #FFFFFF; font-size: 14px; font-weight: 500; padding: 8px 28px; border-radius: 6px;">
                                Close
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection
