<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <style>
    [x-cloak] { display: none !important; }
</style>
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100">
            @include('layouts.sidebar')

        </div>
        <!-- Global Toast Container -->
        <div id="global-toast-container" class="fixed bottom-5 right-5 z-[9999] flex flex-col gap-2 pointer-events-none"></div>

        <script>
            // Global showToast utility for all pages
            function showToast(message, type = 'success', opts = {}) {
                const container = document.getElementById('global-toast-container');
                if(!container) return;
                const toast = document.createElement('div');
                toast.className = 'pointer-events-auto max-w-xs w-full bg-white border p-3 rounded-lg shadow-lg flex items-start gap-3 transform transition-all';
                if(type === 'success') toast.classList.add('border-green-200');
                else if(type === 'error') toast.classList.add('border-red-200');
                else toast.classList.add('border-gray-200');

                const icon = document.createElement('div');
                icon.innerHTML = type === 'success' ? '<svg class="w-5 h-5 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>' : '<svg class="w-5 h-5 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>';
                const msg = document.createElement('div');
                msg.className = 'text-sm text-gray-800';
                msg.textContent = message;

                toast.appendChild(icon);
                toast.appendChild(msg);
                container.appendChild(toast);

                // animate in
                toast.style.opacity = '0';
                toast.style.transform = 'translateY(6px)';
                requestAnimationFrame(() => { toast.style.opacity = '1'; toast.style.transform = 'translateY(0)'; });

                const timeout = (opts.timeout !== undefined) ? opts.timeout : 3500;
                setTimeout(() => {
                    toast.style.opacity = '0';
                    toast.style.transform = 'translateY(8px)';
                    setTimeout(() => toast.remove(), 300);
                }, timeout);
            }
        </script>
        <!-- Global Confirm Dialog (promise based) -->
        <div id="global-confirm" class="fixed inset-0 z-[9998] hidden items-center justify-center bg-black/40">
            <div class="bg-white rounded-lg p-6 w-full max-w-md mx-4">
                <div id="global-confirm-message" class="text-gray-700 mb-4"></div>
                <div class="flex justify-end gap-3">
                    <button id="global-confirm-cancel" class="px-4 py-2 rounded bg-gray-100">Batal</button>
                    <button id="global-confirm-ok" class="px-4 py-2 rounded bg-blue-600 text-white">Konfirmasi</button>
                </div>
            </div>
        </div>

        <script>
            function confirmDialog(message) {
                return new Promise((resolve) => {
                    const wrap = document.getElementById('global-confirm');
                    const msg = document.getElementById('global-confirm-message');
                    const ok = document.getElementById('global-confirm-ok');
                    const cancel = document.getElementById('global-confirm-cancel');
                    if(!wrap || !msg || !ok || !cancel) return resolve(false);
                    msg.textContent = message;
                    wrap.classList.remove('hidden');

                    const cleanup = () => {
                        wrap.classList.add('hidden');
                        ok.removeEventListener('click', onOk);
                        cancel.removeEventListener('click', onCancel);
                    };
                    const onOk = () => { cleanup(); resolve(true); };
                    const onCancel = () => { cleanup(); resolve(false); };
                    ok.addEventListener('click', onOk);
                    cancel.addEventListener('click', onCancel);
                });
            }

            // Intercept forms with data-confirm attribute
            document.addEventListener('submit', function(e) {
                const form = e.target;
                if(form && form.dataset && form.dataset.confirm) {
                    e.preventDefault();
                    confirmDialog(form.dataset.confirm).then(ok => {
                        if(ok) form.submit();
                    });
                }
            }, true);
        </script>
        @stack('scripts')
    </body>
</html>
