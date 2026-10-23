<script>
// ─── Helper functions ───
function pa(id){
    let e = document.getElementById(id);
    if(!e) return 0;
    let s = (e.innerText || '').replace(/[₹,\s]/g,'');
    let neg = s.includes('(-)') || s.startsWith('-');
    s = s.replace('(-)','').replace(/^-/,'');
    return (parseFloat(s)||0) * (neg ? -1 : 1);
}
function fi(n){
    if(n===0) return '-';
    let neg = n < 0;
    n = Math.abs(n);
    let s = n.toFixed(0), l = s.slice(-3), r = s.slice(0, s.length - 3);
    if(r) l = r.replace(/\B(?=(\d{2})+(?!\d))/g, ',') + ',' + l;
    return (neg ? '(-) ' : '') + l;
}
function sc(id,v){
    let e = document.getElementById(id);
    if(e) e.innerText = fi(v);
}
function sv(id,v){
    let e = document.getElementById(id);
    if(e) e.innerText = '₹ ' + fi(v);
}
function sm(...ids){
    return ids.reduce((a,i) => a + pa(i), 0);
}

// ─── Save Item via AJAX ───
function saveItem(el) {
    let val = el.innerText;
    let id = el.getAttribute('data-id');
    let url = el.getAttribute('data-update-url');
    fetch(url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ amount: val })
    }).then(() => calc());
}

// ─── Main calculation ───
function calc(){
    // Capital
    let cap = sm('cap1','cap2','cap3','cap4','gf1','gf2','gf3','gf4','gf5','gf6','gf7','gf11') - pa('cap5');
    sc('capTot', cap);
    let sl = sm('sl1','sl2','sl3','sl4','sl5'); sc('slTot', sl);
    let ul = sm('ul1','ul2','ul3','ul4','ul5'); sc('ulTot', ul);
    let cl = pa('cl1'); sc('clTot', cl);
    let oth = sm('ol1','ol2','oe1','oe2','at1','at2'); sc('othLiabTot', oth);
    let totalLiab = cap + sl + ul + cl + oth;
    sc('liabGT', totalLiab);
    sc('s-liab', totalLiab);

    // Assets
    let ia = sm('ia1','ia2','ia3','ia4','ia5'); sc('iaTot', ia);
    let fa = sm('fa1_total','fa2_total','fa3_total','fa4_total','fa5_total','fa6_total','fa7_total','fa8_total');
    sc('faTot', fa);
    let inv = sm('inv1','inv2','inv3'); sc('invTot', inv);
    let ca = sm('ca1','ca2','ca3','ca3d','oa1','oa2','oa3','oa4','oa6'); sc('caTot', ca);
    let bk = sm('bk1','bk2','bk3','bk4'); sc('bkTot', bk);
    let totalAsset = ia + fa + inv + ca + bk;
    sc('assetGT', totalAsset);
    sc('s-asset', totalAsset);

    // Summary
    sc('s-cap', cap); sc('s-sl', sl); sc('s-ul', ul); sc('s-oth', oth); sc('s-cl', cl);
    sc('s-ia', ia); sc('s-fa', fa); sc('s-invest', inv); sc('s-ca', ca); sc('s-bk', bk);

    // Right Panel KPIs (if exists)
    sv('rp-asset', totalAsset); sv('rp-liab', totalLiab); sv('rp-nw', cap);
    let curLiab = cl + oth; let curAsset = ca + bk;
    let cr = curLiab > 0 ? curAsset / curLiab : 0;
    let crEl = document.getElementById('rp-cr');
    if(crEl) crEl.innerText = cr.toFixed(2) + 'x';
    let wc = curAsset - curLiab;
    let wcEl = document.getElementById('rp-wc');
    if(wcEl) { wcEl.innerText = fi(wc); wcEl.style.color = wc >= 0 ? '#e65100' : 'var(--red)'; }
    sv('rp-cash', bk);
}

// ─── Toggle sections ───
function tog(id){
    let el = document.getElementById(id);
    if(el){
        el.classList.toggle('collapsed');
        let ico = document.getElementById(id + '-ico');
        if(ico) ico.className = el.classList.contains('collapsed') ? 'bi bi-plus-square' : 'bi bi-dash-square';
    }
}

// ─── Event listeners ───
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('[contenteditable="true"]').forEach(el => {
        el.addEventListener('blur', saveItem);
        el.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') { e.preventDefault(); this.blur(); }
            if (e.key === 'Escape') this.blur();
        });
    });
    calc();
});
</script>