<style>
    /* Blur ~60% effect for background when modal open */
    .page-blur {
        filter: blur(6px);
        opacity: 0.6;
        transition: filter 180ms ease, opacity 180ms ease;
        pointer-events: none;
    }
</style>

<!-- Toast + confirm modal copied from users (minimal) -->
<div x-data="{show:false,message:'',color:'bg-green-500',timer:null,open(payload){ let p = payload || {}; this.message = p.message || p[1] || ''; this.color = (p.type==='red'?'bg-red-500':(p.type==='orange'?'bg-orange-500':'bg-green-500')); this.show=true; clearTimeout(this.timer); this.timer=setTimeout(()=>this.show=false,3500);} }" x-init="window.addEventListener('toast', e=>open(e.detail));" class="fixed bottom-6 right-6 z-50 px-4 sm:px-6">
    <div x-show="show" class="pointer-events-auto">
        <div class="max-w-sm w-full bg-white dark:bg-gray-800 rounded-lg shadow-xl border overflow-hidden flex items-center">
            <div :class="color + ' flex items-center justify-center w-12 h-12'"></div>
            <div class="px-4 py-3 flex-1 text-sm text-gray-900 dark:text-gray-100"><div x-text="message"></div></div>
            <div class="px-2"><button @click="show=false;clearTimeout(timer)" class="p-2">×</button></div>
        </div>
    </div>
</div>

<div id="confirm-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black bg-opacity-40 p-4">
    <div id="confirm-panel" class="bg-white dark:bg-gray-800 rounded-lg shadow-2xl max-w-lg w-full overflow-hidden">
        <div class="p-6 text-center">
            <h3 id="confirm-title" class="text-xl font-semibold">¿Estás seguro?</h3>
            <div id="confirm-text" class="mt-2 text-sm text-gray-600">Esta acción no se puede deshacer.</div>
        </div>
        <div class="px-6 pb-6 pt-0 bg-gray-50 dark:bg-gray-900 flex justify-center gap-3">
            <button id="confirm-cancel" class="px-4 py-2 rounded-md bg-white border">Cancelar</button>
            <button id="confirm-ok" class="px-4 py-2 rounded-md bg-red-600 text-white">Confirmar</button>
        </div>
    </div>
</div>

<!-- Consultorio modal (AJAX) -->
<div id="consultorio-modal" class="fixed inset-0 z-50 hidden flex items-center justify-center bg-black bg-opacity-50 p-4">
    <div id="consultorio-panel" class="transform transition-all duration-200 ease-out opacity-0 scale-95 bg-white dark:bg-gray-800 rounded-lg shadow-2xl max-w-2xl w-full overflow-hidden">
        <div class="p-6">
            <h3 id="consultorio-title" class="text-lg font-semibold text-gray-900 dark:text-gray-100">Consultorio</h3>
            <div id="consultorio-body" class="mt-3 text-sm text-gray-700 dark:text-gray-200"></div>
        </div>
        <div class="px-6 pb-6 pt-0 bg-gray-50 dark:bg-gray-900 flex justify-end">
            <button id="consultorio-close" class="px-4 py-2 rounded bg-gray-200 dark:bg-gray-700 dark:text-gray-100">Cerrar</button>
        </div>
    </div>
</div>

<script>
    async function openConsultorioModal(officeId){
        const url = `<?php echo e(url('/medical-offices')); ?>/${officeId}?ajax=1`;
        try {
            const res = await fetch(url, { headers: { 'Accept': 'application/json' } });
            if (!res.ok) throw new Error('Fetch failed');
            const data = await res.json();
            const modal = document.getElementById('consultorio-modal');
            const titleEl = document.getElementById('consultorio-title');
            const bodyEl = document.getElementById('consultorio-body');
            titleEl.textContent = data.name || 'Consultorio';
            // Clear body
            while (bodyEl.firstChild) bodyEl.removeChild(bodyEl.firstChild);

            const pAddr = document.createElement('p');
            pAddr.className = 'text-sm text-gray-600';
            pAddr.textContent = data.address || 'Sin dirección';
            bodyEl.appendChild(pAddr);

            const pLoc = document.createElement('p');
            pLoc.className = 'text-xs text-gray-500 mt-1';
            pLoc.textContent = [data.province || '', data.city || ''].filter(Boolean).join(' · ');
            bodyEl.appendChild(pLoc);

            // Insert map if coordinates are available
            const lat = parseFloat(data.latitude);
            const lon = parseFloat(data.longitude);
            if (!Number.isNaN(lat) && !Number.isNaN(lon)) {
                const mapWrap = document.createElement('div');
                mapWrap.className = 'mt-3';
                const src = `https://www.google.com/maps?q=${lat},${lon}&z=17&output=embed`;
                const iframe = document.createElement('iframe');
                iframe.src = src;
                iframe.width = '100%';
                iframe.height = '300';
                iframe.style.border = '1px solid rgba(0,0,0,0.1)';
                iframe.loading = 'lazy';
                mapWrap.appendChild(iframe);
                const oUrl = document.createElement('div');
                oUrl.className = 'text-xs text-gray-500 mt-1';
                const link = document.createElement('a');
                // Universal Google Maps link — mobile OS/browsers usually open the native app if installed
                link.href = `https://www.google.com/maps/search/?api=1&query=${lat},${lon}`;
                link.target = '_blank';
                link.rel = 'noopener noreferrer';
                link.textContent = 'Abrir en Google Maps';
                oUrl.appendChild(link);
                mapWrap.appendChild(oUrl);
                bodyEl.appendChild(mapWrap);
            } else if (data.address) {
                const mapLink = document.createElement('p');
                mapLink.className = 'text-xs text-gray-500 mt-3';
                const q = encodeURIComponent([data.address, data.city, data.province].filter(Boolean).join(', '));
                const urlSearch = `https://www.google.com/maps/search/?api=1&query=${q}`;
                const a = document.createElement('a');
                a.href = urlSearch; a.target = '_blank'; a.rel = 'noopener noreferrer';
                a.textContent = 'Buscar ubicación en Google Maps';
                mapLink.appendChild(a);
                bodyEl.appendChild(mapLink);
            }

            const panel = document.getElementById('consultorio-panel');
            modal.classList.remove('hidden');
            // apply blur to background
            const root = document.getElementById('page-root');
            if (root) root.classList.add('page-blur');
            requestAnimationFrame(() => {
                panel.classList.remove('opacity-0','-translate-y-2','scale-95');
                panel.classList.add('opacity-100','translate-y-0','scale-100');
            });
        } catch (err) {
            console.error(err);
            alert('No se pudo cargar el consultorio.');
        }
    }

    document.addEventListener('click', function(e){
        const a = e.target.closest && e.target.closest('.consultorio-link');
        if (!a) return;
        e.preventDefault();
        const id = a.getAttribute('data-id');
        if (id) openConsultorioModal(id);
    });

    document.getElementById('consultorio-close')?.addEventListener('click', function(){
        const modal = document.getElementById('consultorio-modal');
        const panel = document.getElementById('consultorio-panel');
        panel.classList.remove('opacity-100','translate-y-0','scale-100');
        panel.classList.add('opacity-0','-translate-y-2','scale-95');
        const rootClose = document.getElementById('page-root');
        if (rootClose) rootClose.classList.remove('page-blur');
        setTimeout(() => modal.classList.add('hidden'), 200);
    });
    document.getElementById('consultorio-modal')?.addEventListener('click', function(e){ if (e.target === this) {
        const modal = this;
        const panel = document.getElementById('consultorio-panel');
        panel.classList.remove('opacity-100','translate-y-0','scale-100');
        panel.classList.add('opacity-0','-translate-y-2','scale-95');
        const rootClose2 = document.getElementById('page-root');
        if (rootClose2) rootClose2.classList.remove('page-blur');
        setTimeout(() => modal.classList.add('hidden'), 200);
    } });
</script>

<script>
    (function(){
        const modal=document.getElementById('confirm-modal');
        const panel=document.getElementById('confirm-panel');
        const btnOk=document.getElementById('confirm-ok');
        const btnCancel=document.getElementById('confirm-cancel');
        let pending=null;
        function show(){
            modal.classList.remove('hidden');
            const r=document.getElementById('page-root'); if(r) r.classList.add('page-blur');
        }
        function hide(){
            modal.classList.add('hidden');
            const r2=document.getElementById('page-root'); if(r2) r2.classList.remove('page-blur');
            pending=null;
        }
        window.confirmAction=function(action,id){
            pending={action,id};
            document.getElementById('confirm-title').textContent = action==='delete' ? 'Confirmar eliminación' : (action==='restore' ? 'Confirmar restauración' : 'Confirmar');
            show(); btnOk.focus();
        };
        btnCancel.addEventListener('click', hide);
        modal.addEventListener('click', e => { if (e.target === modal) hide(); });
        btnOk.addEventListener('click', ()=>{
            if(!pending) return hide();
            if(window.Livewire && typeof Livewire.emit === 'function') Livewire.emit('confirmAction', pending.action, pending.id);
            else window.dispatchEvent(new CustomEvent('confirmAction',{detail:pending}));
            hide();
        });
    })();
</script>
<?php /**PATH C:\laragon\www\medicall\resources\views/livewire/_modals.blade.php ENDPATH**/ ?>