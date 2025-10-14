@props(['id', 'title', 'message', 'confirmText' => 'Confirmer', 'cancelText' => 'Annuler', 'type' => 'danger'])

<div 
    x-data="{ show: false }" 
    x-show="show" 
    x-on:open-modal-{{ $id }}.window="show = true"
    x-on:close-modal.window="show = false"
    x-on:keydown.escape.window="show = false"
    class="fixed inset-0 z-50 overflow-y-auto" 
    style="display: none;"
    x-cloak
>
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        {{-- Overlay --}}
        <div 
            x-show="show" 
            x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 transition-opacity bg-black bg-opacity-75" 
            @click="show = false"
        ></div>

        <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>

        {{-- Modal --}}
        <div 
            x-show="show" 
            x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            class="inline-block align-bottom bg-gray-800 rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-gray-700"
        >
            <div class="bg-gray-800 px-6 pt-6 pb-4">
                <div class="flex items-start">
                    <div class="flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full 
                        {{ $type === 'danger' ? 'bg-red-500/20' : 'bg-blue-500/20' }}">
                        <i class="fas {{ $type === 'danger' ? 'fa-exclamation-triangle text-red-400' : 'fa-info-circle text-blue-400' }} text-xl"></i>
                    </div>
                    <div class="ml-4 flex-1">
                        <h3 class="text-xl font-bold text-white mb-2">
                            {{ $title }}
                        </h3>
                        <p class="text-gray-300 text-sm leading-relaxed">
                            {{ $message }}
                        </p>
                    </div>
                </div>
            </div>
            <div class="bg-gray-900/50 px-6 py-4 flex flex-col-reverse sm:flex-row sm:justify-end gap-3">
                <button 
                    type="button" 
                    @click="show = false"
                    class="px-6 py-2.5 bg-gray-700 text-white rounded-lg hover:bg-gray-600 transition font-semibold"
                >
                    {{ $cancelText }}
                </button>
                <button 
                    type="button" 
                    @click="$dispatch('confirm-action-{{ $id }}'); show = false"
                    class="px-6 py-2.5 rounded-lg transition font-semibold
                        {{ $type === 'danger' ? 'bg-red-600 hover:bg-red-700 text-white' : 'bg-blue-600 hover:bg-blue-700 text-white' }}"
                >
                    {{ $confirmText }}
                </button>
            </div>
        </div>
    </div>
</div>
