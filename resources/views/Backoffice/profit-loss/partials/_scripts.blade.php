<script>
/* ── HELPERS ── */
function pa(id){
  let el=document.getElementById(id);
  if(!el)return 0;
  let s=(el.innerText||el.textContent||'').replace(/[₹,\s]/g,'');
  let neg=s.includes('(-)')||s.startsWith('-');
  s=s.replace('(-)','').replace(/^-/,'');
  return (parseFloat(s)||0)*(neg?-1:1);
}
function fi(n){
  if(!n&&n!==0)return'-';
  let neg=n<0;n=Math.abs(n);
  if(n===0)return'-';
  let s=n.toFixed(0);
  let l=s.slice(-3),r=s.slice(0,s.length-3);
  if(r)l=r.replace(/\B(?=(\d{2})+(?!\d))/g,',')+','+l;
  return(neg?'(-) ':'')+l;
}
function sc(id,v){let e=document.getElementById(id);if(e)e.innerText=(v===0?'-':fi(v));}
function pct(v){return isNaN(v)||!isFinite(v)?'0.00%':v.toFixed(2)+'%';}
function sumIds(ids){return ids.reduce((a,id)=>a+pa(id),0);}

/* ── MAIN CALCULATE ── */
function calc(){
  let gp=pa('gpBD');
  let s1=sumIds(['s1r1','s1r2','s1r3','s1r4','s1r5']);
  let s2=sumIds(['s2r1','s2r2','s2r3','s2r4','s2r5','s2r6','s2r7','s2r8']);
  let s3=sumIds(['s3r1','s3r2','s3r3','s3r4']);
  let s4=sumIds(['s4r1','s4r2','s4r3','s4r4','s4r5','s4r6']);
  let s5=sumIds(['s5r1','s5r2','s5r3']);
  let s6=sumIds(['s6r1','s6r2','s6r3','s6r4','s6r5','s6r6','s6r7','s6r8','s6r9']);
  let s7=sumIds(['s7r1','s7r2','s7r3','s7r4','s7r5']);
  let s8=sumIds(['s8r1','s8r2','s8r3','s8r4','s8r5','s8r6','s8r7','s8r8']);
  let dep=pa('depAmt'),bd=pa('bdAmt');
  sc('s1Tot',s1);sc('s2Tot',s2);sc('s3Tot',s3);sc('s4Tot',s4);
  sc('s5Tot',s5);sc('s6Tot',s6);sc('s7Tot',s7);sc('s8Tot',s8);
  let totalExp=s1+s2+s3+s4+s5+s6+s7+s8+dep+bd;
  let ci1=pa('ci1'),ci2=pa('ci2'),ci3=pa('ci3'),ci4=pa('ci4'),ci5=pa('ci5'),ci6=pa('ci6');
  sc('ci1T',ci1);sc('ci2T',ci2);sc('ci3T',ci3);sc('ci4T',ci4);sc('ci5T',ci5);sc('ci6T',ci6);
  let otherInc=ci1+ci2+ci3+ci4+ci5;
  let othProfit=ci6;
  sc('ciTot',otherInc);sc('ci6Tot',othProfit);
  let totalInc=otherInc+othProfit;
  let np=(gp+totalInc)-totalExp;
  sc('npAmt',Math.max(np,0));
  let drTotal=totalExp+(np>0?np:0);
  sc('drGT1',totalExp);sc('drGT2',drTotal);
  sc('crGT1',gp+totalInc);sc('crGT2',drTotal);

  document.getElementById('rp-gp').innerText='₹'+fi(gp);
  document.getElementById('rp-exp').innerText='₹'+fi(totalExp);
  document.getElementById('rp-inc').innerText='₹'+fi(totalInc);
  document.getElementById('rp-np').innerText='₹'+fi(np>0?np:0);
  document.getElementById('rp-dep').innerText='₹'+fi(dep);
  document.getElementById('rp-bd').innerText='₹'+fi(bd);
  let ebitda=np+dep+bd;
  document.getElementById('rp-ebitda').innerText='₹'+fi(ebitda>0?ebitda:0);
  let base=gp+totalInc;
  let npRatio=base>0?(np/base)*100:0;
  document.getElementById('rpNpRatio').innerText=pct(npRatio);
  let arc=188.5,off=arc-Math.min(Math.max(npRatio/100,0),1)*arc;
  document.getElementById('npRing').setAttribute('stroke-dashoffset',off.toFixed(1));
  document.getElementById('npRing').setAttribute('stroke',npRatio>=0?'#1565c0':'#c62828');
  let mx=Math.max(gp,totalExp,totalInc,1);
  document.getElementById('rp-gpBar').style.width=Math.min(gp/mx*90,90)+'%';
  document.getElementById('rp-expBar').style.width=Math.min(totalExp/mx*90,90)+'%';
  document.getElementById('rp-incBar').style.width=Math.min(totalInc/mx*90,90)+'%';
  document.getElementById('rp-npBar').style.width=Math.min(Math.max(np,0)/mx*90,90)+'%';
  let npEl=document.getElementById('rp-np');
  npEl.style.color=np>=0?'#e65100':'var(--red)';
}

/* ── TOGGLE SECTIONS ── */
function toggleSec(id){
  let el=document.getElementById(id);
  let ico=document.getElementById(id+'-ico');
  if(!el || !ico) return;
  el.classList.toggle('collapsed');
  ico.className=el.classList.contains('collapsed')?'bi bi-plus-square':'bi bi-dash-square';
}

/* ── ADD ROW ── */
let arc=0;
function addExpRow(){
  arc++;
  let id='xr'+arc;
  let tbl=document.getElementById('plBody');
  let npR=document.getElementById('npRow');
  if(!npR) return;
  let newR=tbl.insertBefore(document.createElement('tr'),npR);
  newR.className='dr';
  newR.innerHTML=`
    <td><div class="td-pt"><span class="bul">♦</span>
      <span contenteditable="true" style="outline:none;cursor:text;min-width:80px;display:inline-block;">New Expense</span>
    </div></td>
    <td class="td-rs">₹</td>
    <td class="td-am" contenteditable="true" id="${id}" oninput="calc()">0</td>
    <td class="td-rs"></td>
    <td class="td-am blank" style="border-right:2px solid #4a6fa5;"></td>
    <td class="dc"></td>
    <td colspan="5" style="background:#f7faff;border-left:1px solid #4a6fa5;"></td>`;
  calc();
}

/* ── EXPORT PDF ── */
function exportPDF(){
  showToast('PDF export ready! (Simulation)','success');
}

/* ── FINALIZE & SAVE ── */
function finalizeReport(){
  if(confirm('Finalize Profit & Loss Account for FY 2024–25? This cannot be undone.')){
    document.getElementById('wmTxt').style.display='none';
    document.getElementById('rp-stat').innerText='FINALIZED';
    document.getElementById('rp-stat').style.color='#1b5e20';
    showToast('P&L Account Finalized!','success');
  }
}
function saveDraft(){showToast('Draft saved successfully!','success');}
function showToast(msg,type){
  let t=document.createElement('div');
  t.style.cssText=`position:fixed;bottom:38px;right:16px;z-index:9999;
    background:${type==='success'?'#00695c':'#c62828'};color:#fff;
    padding:9px 16px;border-radius:6px;font-size:11.5px;font-weight:600;
    box-shadow:0 4px 16px rgba(0,0,0,.2);display:flex;align-items:center;gap:7px;
    font-family:var(--font-sans);animation:fadeUp .25s ease;max-width:340px;`;
  t.innerHTML=`<i class="bi bi-${type==='success'?'check-circle-fill':'exclamation-triangle-fill'}"></i>${msg}`;
  document.body.appendChild(t);
  setTimeout(()=>t.remove(),2700);
}

/* ── CA SIGNATURE UPLOAD ── */
function handleCaSignatureUpload(file){
  if(!file)return;
  const reader=new FileReader();
  reader.onload=function(e){
    const imgData=e.target.result;
    const wrapper=document.getElementById('caSigDisplay');
    if(wrapper) wrapper.innerHTML=`<img src="${imgData}" alt="CA Signature" />`;
    try{localStorage.setItem('caSignature',imgData);}catch(ex){}
  };
  reader.readAsDataURL(file);
}
function clearCaSignature(){
  const wrapper=document.getElementById('caSigDisplay');
  if(wrapper) wrapper.innerHTML=`<div class="ft-signature-line"></div>`;
  try{localStorage.removeItem('caSignature');}catch(ex){}
  document.getElementById('caSigUpload').value='';
}
function loadSavedSignature(){
  try{
    const saved=localStorage.getItem('caSignature');
    if(saved){
      const wrapper=document.getElementById('caSigDisplay');
      if(wrapper) wrapper.innerHTML=`<img src="${saved}" alt="CA Signature" />`;
    }
  }catch(ex){}
}
document.getElementById('caSigUpload').addEventListener('change',function(e){
  if(this.files&&this.files.length>0)handleCaSignatureUpload(this.files[0]);
});
loadSavedSignature();

/* ── KEYBOARD ── */
document.addEventListener('keydown',e=>{
  if(e.ctrlKey&&e.key==='s'){e.preventDefault();saveDraft();}
  if(e.ctrlKey&&e.key==='p'){e.preventDefault();window.print();}
  if(e.key==='F5'){e.preventDefault();calc();}
});
document.querySelectorAll('[contenteditable=true]').forEach(el=>{
  el.addEventListener('keydown',function(e){
    if(e.key==='Enter'){e.preventDefault();this.blur();calc();}
    if(e.key==='Escape')this.blur();
  });
  el.addEventListener('focus',function(){
    let r=document.createRange(),s=window.getSelection();
    r.selectNodeContents(this);s.removeAllRanges();s.addRange(r);
  });
  el.addEventListener('blur',calc);
});

/* ── CLOCK ── */
function tick(){
  let n=new Date();
  let el=document.getElementById('statusTime');
  if(el){
    el.textContent=
      String(n.getHours()).padStart(2,'0')+':'+
      String(n.getMinutes()).padStart(2,'0')+':'+
      String(n.getSeconds()).padStart(2,'0');
  }
}
setInterval(tick,1000);tick();

calc();
</script>