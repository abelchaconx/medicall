// Lightweight toast component and Swal fallback for Livewire + Alpine
// Mirrors the behavior found in the compiled assets but maintained in source.

window.toastComponent = function(){
    return {
        toasts: [],
        nextId: 1,
        _recent: {},
        init(){
            if(window.__livewire_toast_registered) return;
            window.__livewire_toast_registered = true;

            function parseArgs(){
                let i = '', s = '';
                if(arguments.length === 1){
                    const o = arguments[0];
                    if(Array.isArray(o)) { i = o[0]||''; s = o[1]||''; }
                    else if(o && typeof o === 'object'){ i = o.type || o[0] || ''; s = o.message || o[1] || ''; }
                    else if(typeof o === 'string'){ const a = o.indexOf(','); if(a !== -1){ i = o.slice(0,a); s = o.slice(a+1); } else { i = o; s = ''; } }
                } else { i = arguments[0]||''; s = arguments[1]||''; }
                return [(i||'info').toString().trim(), (s||'').toString().trim()];
            }

            const setup = () => {
                if(window.Livewire && typeof Livewire.on === 'function'){
                    const handler = function(){
                        const [type,message] = parseArgs.apply(null, arguments);
                        window.toastComponent().pushToast(type,message);
                    };
                    Livewire.on('showToast', handler);
                } else {
                    const handler = function(e){
                            // be tolerant of nested wrappers: e.detail may itself contain { detail: payload }
                            let s = e.detail || {};
                            if (s && typeof s === 'object' && s.detail && typeof s.detail === 'object') s = s.detail;
                            const t = (s.type || s[0] || '').toString();
                            const m = (s.message || s[1] || s.text || '').toString();
                            window.toastComponent().pushToast(t || 'info', m || '');
                        };
                    window.addEventListener('showToast', handler);
                }
            };

            if(window.Livewire) setup(); else { const r = setInterval(()=>{ if(window.Livewire){ clearInterval(r); setup(); } }, 50); }
        },
        pushToast(type, message){
            try{
                const key = (type||'') + '|' + (message||'');
                const now = Date.now();
                if(this._recent[key] && now - this._recent[key] < 1000) return;
                this._recent[key] = now;
                for(const k in this._recent) if(now - this._recent[k] > 5000) delete this._recent[k];
            } catch(e){}

            const id = this.nextId++;
            this.toasts.unshift({ id, type, message, show: true });
            setTimeout(()=> this.removeById(id), 3500);
        },
        removeById(id){
            const idx = this.toasts.findIndex(t => t.id === id);
            if(idx !== -1) this.toasts.splice(idx,1);
        }
    };
};

// Fallback handler for swal:confirm events when SweetAlert isn't available
(function(){
    function normalize(obj){ return obj || {}; }
    function handleConfirm(payload){
        const data = payload && payload.detail ? payload.detail : payload || {};
        const message = data.message || '¿Confirmar?';
        const id = data.id ?? null;

        if(window.Swal) return; // let SweetAlert handle it when present

        try{
            if(confirm(message)){
                try{
                    const el = document.querySelector('[wire\\:id]');
                    if(el && window.Livewire && Livewire.find){
                        const c = el.getAttribute('wire:id') || el.getAttribute('wire\\:id');
                        if(c){ Livewire.find(c).call('deleteUser', id); return; }
                    }
                } catch(e){}

                if(window.Livewire && typeof Livewire.emit === 'function'){
                    Livewire.emit('deleteUser', id);
                } else {
                    window.dispatchEvent(new CustomEvent('swal:confirmed', { detail: { id } }));
                }
            }
        } catch(e){ console.warn('swal fallback error', e); }
    }

    if(window.Livewire && typeof Livewire.on === 'function'){
        Livewire.on('swal:confirm', handleConfirm);
    } else {
        window.addEventListener('swal:confirm', handleConfirm);
    }
})();
