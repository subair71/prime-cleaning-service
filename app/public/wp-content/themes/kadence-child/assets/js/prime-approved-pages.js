(() => {
  const button = document.querySelector('.pp-menu-toggle'), nav = document.getElementById('pp-navigation');
  if(!button || !nav)return;
  button.addEventListener('click',()=>{const open=button.getAttribute('aria-expanded')==='true';button.setAttribute('aria-expanded',String(!open));nav.classList.toggle('is-open',!open);});
  document.addEventListener('keydown',e=>{if(e.key==='Escape'){nav.classList.remove('is-open');button.setAttribute('aria-expanded','false');document.querySelector('.pp-services-menu')?.removeAttribute('open');button.focus();}});
})();
/* Enhance editable core Details blocks into a keyboard-accessible checklist tab set. */
document.querySelectorAll('.pp-inclusions').forEach((group, index) => {
  const panels = Array.from(group.querySelectorAll('details'));
  if (!panels.length) return;
  const tabs = document.createElement('div');
  tabs.className = 'pp-checklist-tabs'; tabs.setAttribute('role', 'tablist'); tabs.setAttribute('aria-label', 'Cleaning checklist areas');
  const buttons = panels.map((panel, i) => {
    const button = document.createElement('button'); button.type = 'button'; button.textContent = panel.querySelector('summary').textContent;
    button.id = 'prime-checklist-tab-' + index + '-' + i; panel.id = 'prime-checklist-panel-' + index + '-' + i;
    button.setAttribute('role', 'tab'); button.setAttribute('aria-controls', panel.id); panel.setAttribute('role', 'tabpanel'); panel.setAttribute('aria-labelledby', button.id);
    button.addEventListener('click', () => select(i));
    button.addEventListener('keydown', event => { let target = i; if (event.key === 'ArrowRight') target = (i + 1) % panels.length; else if (event.key === 'ArrowLeft') target = (i + panels.length - 1) % panels.length; else if (event.key === 'Home') target = 0; else if (event.key === 'End') target = panels.length - 1; else return; event.preventDefault(); select(target); buttons[target].focus(); });
    tabs.append(button); return button;
  });
  function select(active) { panels.forEach((panel, i) => { panel.open = i === active; buttons[i].setAttribute('aria-selected', String(i === active)); buttons[i].tabIndex = i === active ? 0 : -1; }); }
  group.prepend(tabs); group.classList.add('pp-tabs-ready'); select(0);
});
