@extends('layouts.app')

@section('title', 'Notifications')

@section('content')
<div class="d-flex">
    @include('customer.sidebar')

    <div class="main-content flex-grow-1">
        <div class="page-header">
            <h4 class="page-title">Notifications</h4>
            <div class="d-flex gap-2">
                @if($notifications->where('is_read', false)->count() > 0)
                    <form action="{{ route('customer.notifications.read-all') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-outline-success btn-sm px-3">Mark All as Read</button>
                    </form>
                @endif
                @if($notifications->count() > 0)
                    <form action="{{ route('customer.notifications.clear-all') }}" method="POST" onsubmit="return confirm('Are you sure you want to clear all notifications?');">
                        @csrf
                        <button type="submit" class="btn btn-outline-danger btn-sm px-3"><i class="fa fa-trash me-1"></i> Clear All</button>
                    </form>
                @endif
            </div>
        </div>

        <div class="content-area">
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush rounded-3">
                        @forelse($notifications as $notif)
                            <li class="list-group-item p-4 {{ $notif->is_read ? 'notification-read' : 'notification-unread' }}">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <span class="badge {{ $notif->is_read ? 'bg-secondary' : 'bg-success' }} px-3 py-1">
                                        {{ $notif->is_read ? 'Read' : 'New' }}
                                    </span>
                                    <small class="text-muted">{{ $notif->created_at->diffForHumans() }}</small>
                                </div>
                                <p class="mb-3 text-dark fw-medium">{{ $notif->message }}</p>
                                <div class="d-flex gap-3 align-items-center">
                                    @if($notif->action_url)
                                        <a href="{{ $notif->action_url }}" class="btn btn-sm btn-success px-3">View Details</a>
                                    @endif
                                    @if(!$notif->is_read)
                                        <form action="{{ route('customer.notifications.read', $notif->id) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-outline-secondary">Mark as Read</button>
                                        </form>
                                    @endif
                                </div>
                            </li>
                        @empty
                            <div class="text-center py-5 text-muted">
                                <i class="fa fa-bell-slash fa-3x mb-3 text-muted"></i>
                                <p class="mb-0">You have no notifications yet.</p>
                            </div>
                        @endforelse
                    </ul>
                </div>
            </div>

            {{-- Pagination --}}
            <div class="mt-4">
                {{ $notifications->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
</div>
@endsection
