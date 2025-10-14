@extends('layouts.app')

@section('page-title', 'Notifications')
@section('page-subtitle', 'Toutes vos notifications')

@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-white">Notifications</h1>
            <p class="text-gray-400 mt-1">Restez informé de toutes les activités</p>
        </div>
        @if($notifications->where('is_read', false)->count() > 0)
        <form method="POST" action="{{ route('notifications.mark-all-read') }}">
            @csrf
            <button type="submit" class="gradient-bg text-white px-6 py-3 rounded-lg hover:opacity-90 transition font-semibold flex items-center gap-2 shadow-lg shadow-blue-500/30">
                <i class="fas fa-check-double"></i>
                <span>Tout marquer comme lu</span>
            </button>
        </form>
        @endif
    </div>

    {{-- Liste des Notifications --}}
    <div class="bg-slate-800 border border-slate-700 rounded-xl shadow-xl overflow-hidden">
        @forelse($notifications as $notification)
        <a href="{{ route('notifications.read', $notification->id) }}" 
           class="block p-6 border-b border-slate-700 hover:bg-slate-700/50 transition {{ !$notification->is_read ? 'bg-slate-700/30' : '' }}">
            <div class="flex items-start gap-4">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 rounded-full 
                        {{ $notification->type === 'ticket_assigned' ? 'bg-blue-500/20' : ($notification->type === 'new_message' ? 'bg-purple-500/20' : 'bg-green-500/20') }} 
                        flex items-center justify-center">
                        <i class="fas 
                            {{ $notification->type === 'ticket_assigned' ? 'fa-user-tag' : ($notification->type === 'new_message' ? 'fa-comment' : 'fa-sync-alt') }} 
                            text-xl
                            {{ $notification->type === 'ticket_assigned' ? 'text-blue-400' : ($notification->type === 'new_message' ? 'text-purple-400' : 'text-green-400') }}">
                        </i>
                    </div>
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex items-center justify-between mb-2">
                        <p class="text-lg font-semibold text-white">{{ $notification->title }}</p>
                        @if(!$notification->is_read)
                        <span class="inline-block w-2.5 h-2.5 bg-blue-500 rounded-full"></span>
                        @endif
                    </div>
                    <p class="text-gray-300 mb-2">{{ $notification->message }}</p>
                    <p class="text-sm text-gray-500 flex items-center gap-2">
                        <i class="fas fa-clock text-xs"></i>
                        {{ $notification->created_at->diffForHumans() }}
                    </p>
                </div>
            </div>
        </a>
        @empty
        <div class="p-12 text-center">
            <i class="fas fa-bell-slash text-5xl text-gray-600 mb-4"></i>
            <p class="text-gray-400 text-lg">Aucune notification</p>
        </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    @if($notifications->hasPages())
    <div class="flex justify-center">
        {{ $notifications->links() }}
    </div>
    @endif
</div>
@endsection