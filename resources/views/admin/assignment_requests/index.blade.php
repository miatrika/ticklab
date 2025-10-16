@extends('layouts.app')

@section('page-title', 'Demandes d’Assignation')
@section('page-subtitle', 'Validation des demandes des techniciens')

@section('content')
<div class="bg-slate-800 border border-slate-700 rounded-xl shadow-xl overflow-hidden">
    <div class="px-6 py-4 border-b border-slate-700">
        <h3 class="text-lg font-bold text-white flex items-center gap-2">
            <i class="fas fa-user-check text-blue-400"></i>
            Demandes d’Assignation
        </h3>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-slate-900 border-b border-slate-700">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-300 uppercase">Ticket</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-300 uppercase">Technicien</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-300 uppercase">Statut</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-300 uppercase">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-700">
                @forelse($requests as $req)
                    <tr class="hover:bg-slate-700/50 transition">
                        <td class="px-6 py-4 text-sm text-white">
                            {{ $req->ticket->title }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-300">
                            {{ $req->technician->name }}
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-3 py-1 text-xs font-semibold rounded-full
                                @if($req->status == 'pending') bg-yellow-500/20 text-yellow-400
                                @elseif($req->status == 'approved') bg-green-500/20 text-green-400
                                @else bg-red-500/20 text-red-400
                                @endif">
                                {{ ucfirst($req->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            @if($req->status == 'pending')
                                <form action="{{ route('admin.assignment-requests.approve', $req->id) }}" method="POST" class="inline">
                                    @csrf
                                    <button class="bg-green-500/20 text-green-400 px-3 py-1 rounded-lg border border-green-500/30 text-xs font-semibold hover:bg-green-500/30">Approuver</button>
                                </form>
                                <form action="{{ route('admin.assignment-requests.reject', $req->id) }}" method="POST" class="inline ml-2">
                                    @csrf
                                    <button class="bg-red-500/20 text-red-400 px-3 py-1 rounded-lg border border-red-500/30 text-xs font-semibold hover:bg-red-500/30">Rejeter</button>
                                </form>
                            @else
                                <span class="text-gray-500 text-sm italic">Aucune action</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-6 py-8 text-center text-gray-400">
                            Aucune demande d’assignation en attente
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

