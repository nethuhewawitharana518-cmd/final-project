@extends('layouts.app')

@section('title', 'User Management')

@section('content')
<div class="d-flex">
    @include('admin.sidebar')

    <div class="main-content flex-grow-1">
        <div class="page-header">
            <h4 class="page-title">User Management</h4>
            <span class="badge bg-danger status-badge active">System Administration</span>
        </div>

        <div class="content-area">
            {{-- Role Filter Tabs --}}
            <div class="d-flex flex-wrap gap-2 mb-4">
                <a href="{{ route('admin.users', ['role' => 'all']) }}" class="btn btn-sm {{ $role == 'all' ? 'btn-danger' : 'btn-outline-secondary' }}">All Users</a>
                <a href="{{ route('admin.users', ['role' => 'customer']) }}" class="btn btn-sm {{ $role == 'customer' ? 'btn-danger' : 'btn-outline-secondary' }}">Customers</a>
                <a href="{{ route('admin.users', ['role' => 'business_owner']) }}" class="btn btn-sm {{ $role == 'business_owner' ? 'btn-danger' : 'btn-outline-secondary' }}">Hotels & Restaurants</a>
                <a href="{{ route('admin.users', ['role' => 'admin']) }}" class="btn btn-sm {{ $role == 'admin' ? 'btn-danger' : 'btn-outline-secondary' }}">Admins</a>
            </div>

            {{-- Users Table --}}
            <div class="card border-0 shadow-sm rounded-3 bg-white">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table fr-table mb-0 align-middle">
                            <thead>
                                <tr>
                                    <th>User Name</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>Role</th>
                                    <th>Registration Date</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($users as $user)
                                <tr>
                                    <td class="fw-bold text-dark">{{ $user->name }}</td>
                                    <td>{{ $user->email }}</td>
                                    <td>{{ $user->phone ?? 'N/A' }}</td>
                                    <td>
                                        @if($user->isAdmin())
                                            <span class="badge bg-danger">Admin</span>
                                        @elseif($user->isBusinessOwner())
                                            <span class="badge bg-success">Business Owner</span>
                                        @else
                                            <span class="badge bg-info">Customer</span>
                                        @endif
                                    </td>
                                    <td>{{ $user->created_at->format('M d, Y H:i') }}</td>
                                    <td>
                                        <span class="status-badge {{ $user->status === 'active' ? 'active' : 'expired' }}">
                                            {{ ucfirst($user->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="d-flex gap-2">
                                            @if($user->id !== auth()->id())
                                                @if($user->status === 'active')
                                                    <form action="{{ route('admin.users.suspend', $user->id) }}" method="POST" onsubmit="return confirm('Suspend this user account?')">
                                                        @csrf
                                                        <button type="submit" class="btn btn-sm btn-outline-warning">Suspend</button>
                                                    </form>
                                                @else
                                                    <form action="{{ route('admin.users.activate', $user->id) }}" method="POST" onsubmit="return confirm('{{ $user->status === 'pending' && $user->isBusinessOwner() ? 'Approve' : 'Activate' }} this user account?')">
                                                        @csrf
                                                        <button type="submit" class="btn btn-sm btn-success">
                                                            {{ $user->status === 'pending' && $user->isBusinessOwner() ? 'Approve' : 'Activate' }}
                                                        </button>
                                                    </form>
                                                @endif
                                                <form action="{{ route('admin.users.delete', $user->id) }}" method="POST" onsubmit="return confirm('Permanently delete this user?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                                </form>
                                            @else
                                                <span class="text-muted small">Current User</span>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center py-5 text-muted">
                                        <i class="fa fa-users fa-2x mb-3 text-muted"></i>
                                        <p class="mb-0">No users found.</p>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- Pagination --}}
            <div class="mt-4">
                {{ $users->appends(request()->input())->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
</div>
@endsection
