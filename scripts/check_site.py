"""Проверка требований HTML/ресурсов без сетевых зависимостей."""
from pathlib import Path
from html.parser import HTMLParser
from urllib.parse import urlsplit,unquote
import json,sys
ROOT=Path(__file__).resolve().parents[1];D=ROOT/'dist'
class Page(HTMLParser):
 def __init__(self):super().__init__();self.tags=[];self.ids=set();self.title=False
 def handle_starttag(self,t,a):
  a=dict(a);self.tags.append((t,a))
  if a.get('id'):self.ids.add(a['id'])
  if t=='title':self.title=True
pages={p.name:Page() for p in D.glob('*.html') if p.name not in ['404.html','_qa.html']}
for file,p in pages.items():p.feed((D/file).read_text())
errors=[]
for file,p in pages.items():
 def fail(msg):errors.append(f'{file}: {msg}')
 if sum(t=='h1' for t,a in p.tags)!=1:fail('нужен ровно один H1')
 if not p.title:fail('нет title')
 if not any(t=='meta' and a.get('name')=='description' for t,a in p.tags):fail('нет description')
 for tag in ['header','nav','main','footer']:
  if not any(t==tag for t,a in p.tags):fail(f'нет {tag}')
 labels={a.get('for') for t,a in p.tags if t=='label'}
 for t,a in p.tags:
  if t=='img':
   for attr in ['alt','width','height']:
    if not a.get(attr):fail('img без '+attr)
  if t in ['input','select','textarea'] and a.get('id') not in labels:fail('поле без label: '+str(a.get('id')))
  for attr in ['href','src']:
   v=a.get(attr,'');u=urlsplit(v)
   if not v or u.scheme or v.startswith('//'):continue
   target=D/unquote(u.path) if u.path else D/file
   if not target.exists():fail('битая ссылка '+v)
   if u.fragment and target.suffix=='.html' and target.name in pages and u.fragment not in pages[target.name].ids:fail('нет якоря '+v)
 for t,a in p.tags:
  if t=='meta' and a.get('property') in ['og:image','og:url'] and not a.get('content','').startswith('https://'):fail('OG URL должен быть абсолютным')
webp=[{'name':f.name,'bytes':f.stat().st_size} for f in (D/'img').glob('*.webp')]
for im in webp:
 if im['bytes']>200000:errors.append('WebP больше 200 КБ: '+im['name'])
result={'pages':len(pages),'errors':errors,'webp':webp}
(ROOT/'docs/local-checks.json').write_text(json.dumps(result,ensure_ascii=False,indent=2))
print(json.dumps(result,ensure_ascii=False,indent=2));sys.exit(bool(errors))
