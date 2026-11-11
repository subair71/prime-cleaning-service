/* Server calculates prices; the browser only guides the enquiry journey. */
(() => {
  'use strict';
  const config = window.primeEstimate;
  if (!config) return;
  const uuid = () => crypto.randomUUID ? crypto.randomUUID() : 'prime-' + Date.now() + '-' + Math.random().toString(36).slice(2) + Math.random().toString(36).slice(2);
  const read = form => { const p = Object.fromEntries(new FormData(form)); p.consent = !!form.elements.consent?.checked; return p; };
  const errors = (form, data = {}) => {
    form.querySelectorAll('[aria-invalid]').forEach(el => el.removeAttribute('aria-invalid'));
    form.querySelectorAll('[data-error]').forEach(el => { el.textContent = ''; });
    const box = form.querySelector('.pe-errors'); box.hidden = !data.message; box.textContent = data.message || '';
    Object.entries(data.errors || {}).forEach(([name, message]) => {
      const target = form.querySelector('[data-error="' + name + '"]');
      if (target) { target.textContent = message; target.id ||= 'pe-error-' + name; }
      form.querySelectorAll('[name="' + name + '"]').forEach(field => { field.setAttribute('aria-invalid', 'true'); if (target) field.setAttribute('aria-describedby', target.id); });
    });
    if (data.message) { box.tabIndex = -1; box.focus(); }
  };
  document.addEventListener('input', event => {
    const field = event.target, form = field.closest('.pe-form');
    if (!form || !field.name) return;
    field.removeAttribute('aria-invalid');
    const target = form.querySelector('[data-error="' + field.name + '"]');
    if (target) target.textContent = '';
  });
  const send = async (action, payload) => {
    let response;
    try { response = await fetch(config.url, { method: 'POST', body: new URLSearchParams({ action: 'pce_v2_' + action, nonce: config.nonce, payload: JSON.stringify(payload) }) }); }
    catch (_) { throw { message: 'Connection interrupted. Your details are still here. Please try again.' }; }
    let result; try { result = await response.json(); } catch (_) { throw { message: 'We could not confirm the response. Please retry; the same enquiry will not be saved twice.' }; }
    if (!result.success) throw result.data || { message: 'Please try again.' };
    return result.data;
  };
  const root = document.querySelector('.pe');
  if (root) {
    const form = root.querySelector('form'), next = form.querySelector('.pe-next'), back = form.querySelector('.pe-back');
    let step = 0, token = '', requestKey = uuid(), busy = false;
    const service = () => form.querySelector('[name="service"]:checked')?.value || '';
    const payload = () => { const p = read(form); p.extras = {}; p.carpets = {}; for (const key of ['oven','fridge','windows','balcony','walls']) p.extras[key] = !!form.elements['extra_' + key].checked; for (const key of ['bedroom','living','hallway','stairs']) p.carpets[key] = form.elements['carpet_' + key].value; p.furnished = form.elements.furnished.checked; return p; };
    const adapt = () => {
      const s = service(); form.querySelector('.pe-frequency').hidden = s !== 'regular'; form.querySelector('.pe-lease').hidden = s !== 'lease'; form.querySelector('.pe-property').hidden = s === 'carpet';form.querySelector('.pe-carpet-only').hidden = s !== 'carpet';form.querySelector('.pe-extras').hidden = s === 'carpet';
      form.querySelector('.pe-unlisted').hidden = form.elements.suburb.value !== 'unlisted';
      const selected = config.suburbs[form.elements.suburb.value]; form.elements.review_suburb.value = selected ? selected.name + ' ' + selected.postcode : form.elements.suburb_name.value; form.querySelector('.pe-zone-message').textContent = selected ? 'Selected suburb: Zone ' + selected.zone : '';
      root.querySelector('.pe-summary-service').textContent = form.querySelector('[name="service"]:checked')?.closest('label').querySelector('strong').textContent || 'Choose a service to get started.';
    };
    const show = focus => {
      form.querySelectorAll('.pe-step').forEach(el => el.hidden = Number(el.dataset.step) !== step);
      root.querySelectorAll('[data-progress]').forEach(el => { el.removeAttribute('aria-current'); if (Number(el.dataset.progress) === Math.min(step,4)) el.setAttribute('aria-current','step'); el.classList.toggle('complete',Number(el.dataset.progress)<step); });
      back.hidden = step === 0; next.textContent = step === 3 ? 'See my estimate →' : step === 4 ? 'Continue to enquiry →' : step === 5 ? 'Send enquiry →' : 'Continue →';
      if (focus) { const heading = form.querySelector('[data-step="' + step + '"] h2'); heading.tabIndex = -1; heading.focus({preventScroll:true}); root.scrollIntoView({block:'start',behavior:'smooth'}); } adapt();
    };
    const validate = () => {
      const p = payload(), e = {};
      if (step === 0 && !p.service) e.service = 'Choose a cleaning service.';
      if (step === 1 && p.service !== 'carpet') ['bedrooms','bathrooms','property'].forEach(k => { if (!p[k]) e[k] = 'Please select an option.'; });
      if (step === 2) { const invalid = Object.values(p.carpets).some(v => !/^\d{1,2}$/.test(v)); if (invalid) e.carpets = 'Use whole quantities from 0 to 99.'; else if(p.service==='carpet' && !Object.values(p.carpets).some(v => Number(v)>0)) e.carpets='Enter at least one carpet quantity.'; }
      if (step === 3 && !p.suburb) e.suburb = 'Choose your suburb, or suburb not listed.';
      if (step === 3 && p.suburb === 'unlisted' && !p.suburb_name.trim()) e.suburb_name = 'Enter your suburb.';
      if (Object.keys(e).length) { errors(form,{message:'Please complete the highlighted fields.',errors:e}); return false; } return true;
    };
    const result = q => {
      const box = root.querySelector('.pe-result'); box.replaceChildren();
      form.querySelector('[data-step="5"] h2 + p').textContent = (q.state === 'custom' ? 'Your custom-quote request is ready.' : 'Your estimate is ready.') + ' Add your contact details so we can follow up.';
      const heading = document.createElement('h3'); heading.className = 'pe-range'; heading.textContent = q.state === 'estimate' ? 'AUD ' + q.low + '–' + q.high : 'Let’s prepare a custom quote';box.append(heading);
      if(q.state==='custom') { const reasons={bedrooms:'Properties with 6+ bedrooms need a tailored quote.',bathrooms:'Properties with 5+ bathrooms need a tailored quote.',furnished:'Furnished End of Lease properties need a tailored quote.',suburb:'Your suburb is not on the approved list.',missing_price:'An applicable approved price is unavailable. We will confirm your quote personally.'}; const ul=document.createElement('ul'); q.reasons.forEach(k=>{const li=document.createElement('li');li.textContent=reasons[k]||'Please send an enquiry for a tailored quote.';ul.append(li);});box.append(ul); }
      else { const details=document.createElement('details'),summary=document.createElement('summary');summary.textContent='View price breakdown';details.append(summary);q.lines.forEach(line=>{const row=document.createElement('div');row.className='pe-price-line';const label=document.createElement('span'),amount=document.createElement('strong');label.textContent=line.label;amount.textContent='AUD '+Number(line.amount).toFixed(2);row.append(label,amount);details.append(row);});const note=document.createElement('p');note.textContent='Minimum charge applied where needed. The range is ±10%, rounded to the nearest AUD 5.';details.append(note);box.append(details); }
    };
    next.addEventListener('click',async()=>{ if(busy)return;errors(form);if(!validate())return;
      busy=true;next.disabled=true;back.disabled=true;const text=next.textContent;next.textContent=step>=3?'Please wait…':text;
      try { if(step===3){const data=await send('quote',payload());token=data.token;result(data.quote);step++;}
        else if(step===5){const data=await send('submit',{...payload(),quote_token:token,request_key:requestKey});root.querySelector('.pe-layout').hidden=true;root.querySelector('.pe-progress').hidden=true;const done=root.querySelector('.pe-success');done.hidden=false;done.querySelector('.pe-reference').textContent=data.reference;const wa=done.querySelector('.pe-send-photos');wa.hidden=!data.whatsapp;if(data.whatsapp)wa.href=data.whatsapp;done.querySelector('.pe-wa-unavailable').hidden=!!data.whatsapp;done.focus();}
        else step++; show(root.querySelector('.pe-success').hidden);
      } catch(e){if(e.expired){step=3;show(true);}errors(form,e);}finally{busy=false;next.disabled=false;back.disabled=false;if(root.querySelector('.pe-success').hidden)show(false);}
    });
    back.addEventListener('click',()=>{if(!busy){errors(form);step=Math.max(0,step-1);show(true);}});
    form.addEventListener('submit',e=>{e.preventDefault();next.click();});form.addEventListener('change',adapt);
    const params=new URLSearchParams(location.search),s=params.get('prime_service');if(['lease','regular','deep','carpet'].includes(s)){form.querySelector('[name="service"][value="'+s+'"]').checked=true;const b=params.get('prime_bedrooms');if(/^[0-6]$/.test(b||''))form.elements.bedrooms.value=b;if(params.get('furnished')==='1')form.elements.furnished.checked=true;}
    show(false);
  }
  document.querySelectorAll('.pe-contact').forEach(form=>{const key=uuid();form.addEventListener('submit',async e=>{e.preventDefault();const button=form.querySelector('button[type="submit"]');if(button.disabled)return;errors(form);button.disabled=true;try{const data=await send('contact',{...read(form),request_key:key});const done=form.querySelector('.pe-contact-success');done.hidden=false;done.textContent='Thank you. Your enquiry has been saved. Reference: '+data.reference+'. This is not a confirmed booking.';button.hidden=true;}catch(err){errors(form,err);}finally{button.disabled=false;}});});
})();
