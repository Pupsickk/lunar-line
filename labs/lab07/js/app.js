/* Простая реализация без сборки и внешних зависимостей. */
'use strict';
const PRODUCTS = [
{id:'one-question',name:'Один вопрос',price:790}, {id:'relationships',name:'Об отношениях',price:1490},
{id:'choice',name:'На перекрёстке',price:1290}, {id:'career',name:'Дело по душе',price:1790},
{id:'month',name:'Мой месяц',price:1990}, {id:'deep',name:'Большая картина',price:2990}
];
const CART_KEY='lunar-line-cart-v1';
const money = value => new Intl.NumberFormat('ru-RU').format(value)+' ₽';
let cart={};
try {const parsed=JSON.parse(localStorage.getItem(CART_KEY)||'{}'); if(parsed && typeof parsed==='object' && !Array.isArray(parsed)) for(const p of PRODUCTS){const q=parsed[p.id];if(Number.isInteger(q)&&q>0&&q<=9) cart[p.id]=q;}} catch { /* Приватный режим или повреждённое хранилище: пустая корзина. */ }
let toastTimer;
function notify(message){const el=document.getElementById('toast');if(!el)return;el.textContent=message;clearTimeout(toastTimer);toastTimer=setTimeout(()=>{el.textContent='';},3500);}
function total(){return PRODUCTS.reduce((sum,p)=>sum+p.price*(cart[p.id]||0),0);}
function persist(){try{localStorage.setItem(CART_KEY,JSON.stringify(cart));}catch{notify('Хранилище недоступно. Корзина сохранится только на этой странице.');}renderCart();}
function renderCart(){
 const count=Object.values(cart).reduce((a,b)=>a+b,0);document.querySelectorAll('[data-cart-count]').forEach(x=>x.textContent=count);
 const items=PRODUCTS.filter(p=>cart[p.id]);const container=document.getElementById('cart-items');
 if(container){container.replaceChildren();if(!items.length){container.innerHTML='<div class="empty-state"><h2>Здесь пока тихо</h2><p>Выберите расклад, который откликается вашему вопросу.</p><a class="btn" href="catalog.html">Открыть каталог ↗</a></div>';}else{for(const p of items){const row=document.createElement('article');row.className='cart-row';row.innerHTML=`<img src="img/${p.id}.webp" alt="Расклад ${p.name}" width="600" height="480"><div><h2><a href="${p.id}.html">${p.name}</a></h2><p>${money(p.price)} за сессию</p></div><div class="cart-controls"><button data-change="${p.id}" data-delta="-1" aria-label="Уменьшить количество: ${p.name}">−</button><span aria-label="Количество">${cart[p.id]}</span><button data-change="${p.id}" data-delta="1" aria-label="Увеличить количество: ${p.name}" ${cart[p.id]>=9?'disabled':''}>+</button><strong>${money(p.price*cart[p.id])}</strong><button data-remove="${p.id}">Удалить ${p.name}</button></div>`;container.append(row);}}
 document.getElementById('cart-summary').hidden=!items.length;document.getElementById('cart-total').textContent=money(total());}
 const order=document.getElementById('order-items');if(order){order.replaceChildren();if(!items.length){order.innerHTML='<p>Корзина пуста. <a href="catalog.html">Выберите расклад</a> перед оформлением.</p>';}else{for(const p of items){const line=document.createElement('div');line.className='order-line';const title=document.createElement('span');title.textContent=`${p.name} × ${cart[p.id]}`;const price=document.createElement('strong');price.textContent=money(p.price*cart[p.id]);line.append(title,price);order.append(line);}}document.getElementById('order-total').textContent='Итого: '+money(total());const submit=document.querySelector('[data-form="order"] button[type="submit"]');if(submit)submit.disabled=!items.length;}
}
document.addEventListener('click',event=>{
 const add=event.target.closest('[data-add]');if(add){const p=PRODUCTS.find(p=>p.id===add.dataset.add);if(!p)return;if((cart[p.id]||0)>=9){notify('Максимум 9 сессий одного расклада.');return;}cart[p.id]=(cart[p.id]||0)+1;persist();notify(`«${p.name}» добавлен в корзину`);}
 const change=event.target.closest('[data-change]');if(change&&cart[change.dataset.change]){const id=change.dataset.change;cart[id]=Math.min(9,cart[id]+Number(change.dataset.delta));if(cart[id]<=0)delete cart[id];persist();}
 const remove=event.target.closest('[data-remove]');if(remove){delete cart[remove.dataset.remove];persist();}
});
document.getElementById('clear-cart')?.addEventListener('click',()=>{cart={};persist();notify('Корзина очищена');});
window.addEventListener('storage',e=>{if(e.key===CART_KEY){location.reload();}});
const burger=document.getElementById('burger'), nav=document.getElementById('nav');
function closeMenu(){nav?.classList.remove('is-open');burger?.classList.remove('is-active');burger?.setAttribute('aria-expanded','false');burger?.setAttribute('aria-label','Открыть меню');}
burger?.addEventListener('click',()=>{const open=nav.classList.toggle('is-open');burger.classList.toggle('is-active',open);burger.setAttribute('aria-expanded',String(open));burger.setAttribute('aria-label',open?'Закрыть меню':'Открыть меню');});
document.addEventListener('keydown',e=>{if(e.key==='Escape'){const open=nav?.classList.contains('is-open');closeMenu();if(open)burger.focus();}});
document.addEventListener('click',e=>{if(!e.target.closest('.header'))closeMenu();});
nav?.querySelectorAll('a').forEach(a=>a.addEventListener('click',closeMenu));
window.matchMedia('(min-width:1100px)').addEventListener('change',closeMenu);
const topButton=document.getElementById('scrollTop');window.addEventListener('scroll',()=>topButton?.classList.toggle('is-visible',window.scrollY>400),{passive:true});topButton?.addEventListener('click',()=>window.scrollTo({top:0,behavior:window.matchMedia('(prefers-reduced-motion: reduce)').matches?'instant':'smooth'}));
const filters=document.querySelectorAll('[data-filter]'), grid=document.getElementById('catalog-grid');
filters.forEach(button=>button.addEventListener('click',()=>{filters.forEach(x=>{const active=x===button;x.classList.toggle('is-active',active);x.setAttribute('aria-pressed',String(active));});let count=0;grid?.querySelectorAll('[data-category]').forEach(c=>{c.hidden=button.dataset.filter!=='all'&&c.dataset.category!==button.dataset.filter;if(!c.hidden)count++;});document.getElementById('catalog-count').textContent='Найдено раскладов: '+count;}));
document.getElementById('sort')?.addEventListener('change',e=>{const cards=Array.from(grid.children);const original=PRODUCTS.map(p=>p.id);cards.sort((a,b)=>e.target.value==='default'?original.indexOf(a.querySelector('[data-add]').dataset.add)-original.indexOf(b.querySelector('[data-add]').dataset.add):(Number(a.dataset.price)-Number(b.dataset.price))*(e.target.value==='desc'?-1:1));cards.forEach(c=>grid.append(c));});
const errors={name:'Введите имя от 2 символов.',email:'Укажите корректный email, например name@example.com.',phone:'Введите телефон от 10 до 20 символов: цифры, +, пробелы, скобки или дефис.',agree:'Подтвердите согласие с политикой конфиденциальности.'};
function validateField(field){if(field.id==='name')field.setCustomValidity(field.value.trim().length<2?'Имя слишком короткое':'');if(field.id==='email')field.setCustomValidity(field.value&&!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(field.value.trim())?'Проверьте email':'');const valid=field.checkValidity();field.classList.toggle('is-invalid',!valid);field.setAttribute('aria-invalid',String(!valid));const error=document.getElementById(field.id+'-error');if(error){error.textContent=valid?'':errors[field.id];error.classList.toggle('is-visible',!valid);}return valid;}
document.querySelectorAll('[data-form]').forEach(form=>{
 const fields=['name','email','phone','agree'].map(id=>form.querySelector('#'+id));
 fields.forEach(f=>{f.addEventListener('blur',()=>{if(f.value||f.id==='agree'||form.dataset.submitted)validateField(f);});f.addEventListener('input',()=>{if(form.dataset.submitted||f.classList.contains('is-invalid'))validateField(f);});});
 form.addEventListener('submit',e=>{e.preventDefault();form.dataset.submitted='true';const results=fields.map(validateField);const first=fields[results.indexOf(false)];if(first){first.focus();return;}const output=form.querySelector('.form-result');if(form.dataset.form==='order'){if(!total()){output.textContent='Сначала добавьте расклад в корзину.';output.focus();return;}const sum=total();const count=Object.values(cart).reduce((a,b)=>a+b,0);cart={};persist();output.textContent=`Демозаказ создан: позиций ${count}, сумма ${money(sum)}. Оплата не списана, данные никуда не отправлены.`;}else output.textContent='Демозаявка успешно проверена. Данные никуда не отправлены.';form.reset();delete form.dataset.submitted;fields.forEach(f=>{f.setCustomValidity('');f.classList.remove('is-invalid');f.removeAttribute('aria-invalid');});output.focus();});
});
renderCart();
