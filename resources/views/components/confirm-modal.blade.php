@props(['name' => 'confirm-modal'])

<div x-data="{
        show: false,
        title: 'Confirm Action',
        message: 'Are you sure you want to proceed?',
        confirmText: 'Confirm',
        cancelText: 'Cancel',
        type: 'warning', // warning, danger, info
        targetFormId: null,
        
        get icon() {
            return {
                warning: 'warning',
                danger: 'error',
                info: 'info'
            }[this.type]
        },
        
        get color() {
            return {
                warning: 'text-orange-500 bg-orange-100',
                danger: 'text-red-500 bg-red-100',
                info: 'text-blue-500 bg-blue-100'
            }[this.type]
        },

        get btnColor() {
            return {
                warning: 'bg-orange-600 hover:bg-orange-700 focus:ring-orange-500',
                danger: 'bg-red-600 hover:bg-red-700 focus:ring-red-500',
                info: 'bg-blue-600 hover:bg-blue-700 focus:ring-blue-500'
            }[this.type]
        }
    }" x-on:open-confirmation.window="
        show = true;
        title = $event.detail.title || title;
        message = $event.detail.message || message;
        confirmText = $event.detail.confirmText || confirmText;
        type = $event.detail.type || 'warning';
        targetFormId = $event.detail.formId || null;
    " x-on:keydown.escape.window="show = false" style="display: none;" x-show="show"
    class="fixed inset-0 z-[100] overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <!-- Backdrop -->
    <div x-show="show" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
        class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" @click="show = false">
    </div>

    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        <div x-show="show" x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            class="relative inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">

            <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <div class="sm:flex sm:items-start">
                    <div :class="color"
                        class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full sm:mx-0 sm:h-10 sm:w-10">
                        <span class="material-symbols-outlined text-2xl" x-text="icon"></span>
                    </div>
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-gray-100" id="modal-title"
                            x-text="title"></h3>
                        <div class="mt-2">
                            <p class="text-sm text-gray-500 dark:text-gray-400" x-text="message"></p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-gray-50 dark:bg-gray-700/50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                <button type="button" :class="btnColor"
                    class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 text-base font-medium text-white focus:outline-none focus:ring-2 focus:ring-offset-2 sm:ml-3 sm:w-auto sm:text-sm"
                    @click="if(targetFormId) document.getElementById(targetFormId).submit(); show = false;">
                    <span x-text="confirmText"></span>
                </button>
                <button type="button"
                    class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 dark:border-gray-600 shadow-sm px-4 py-2 bg-white dark:bg-gray-800 text-base font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm"
                    @click="show = false">
                    <span x-text="cancelText"></span>
                </button>
            </div>
        </div>
    </div>
</div>