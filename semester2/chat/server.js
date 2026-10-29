'use strict';
const http=require('node:http');const fs=require('node:fs');const path=require('node:path');const {randomUUID}=require('node:crypto');const {WebSocketServer,WebSocket}=require('ws');
function createChat({port=8080,host='127.0.0.1'}={}){
 const clients=new Map(),history=[];const server=http.createServer((req,res)=>{
  const file={'/':'index.html','/script.js':'script.js','/style.css':'style.css'}[req.url?.split('?')[0]];
  if(!file){res.writeHead(404);return res.end('Not found');}res.setHeader('Content-Type',file.endsWith('.js')?'text/javascript; charset=utf-8':file.endsWith('.css')?'text/css; charset=utf-8':'text/html; charset=utf-8');fs.createReadStream(path.join(__dirname,'public',file)).pipe(res);
 });
 const wss=new WebSocketServer({server,maxPayload:16384});
 function send(ws,data){if(ws.readyState===WebSocket.OPEN)ws.send(JSON.stringify(data));}
 function broadcast(data,except){for(const ws of clients.keys())if(ws!==except)send(ws,data);}
 function system(message){broadcast({type:'system',message,id:randomUUID(),timestamp:new Date().toISOString()});}
 function users(){broadcast({type:'users',users:[...clients.values()].map(({id,name})=>({id,name}))});}
 wss.on('connection',(ws,req)=>{
  const origin=req.headers.origin;if(origin){let o;try{o=new URL(origin);}catch{ws.close(1008,'Invalid origin');return;}if(!['localhost','127.0.0.1','[::1]'].includes(o.hostname)){ws.close(1008,'Local origin required');return;}}
  ws.alive=true;ws.on('pong',()=>ws.alive=true);let typingTimer;let windowStart=Date.now(),count=0;
  ws.on('message',raw=>{
   if(Date.now()-windowStart>10000){windowStart=Date.now();count=0;}if(++count>50){send(ws,{type:'error',message:'Слишком часто. Подождите.'});return;}
   let d;try{d=JSON.parse(raw);}catch{send(ws,{type:'error',message:'Неверный JSON'});return;}if(!d||typeof d!=='object')return;
   if(d.type==='join'){
    if(clients.has(ws))return;const name=typeof d.name==='string'?d.name.trim():'';if(!name||name.length>40){send(ws,{type:'error',message:'Имя должно содержать 1–40 символов'});return;}
    const id=randomUUID();clients.set(ws,{id,name});send(ws,{type:'welcome',id,history});system(`Пользователь ${name} вошёл в чат`);users();return;
   }
   const user=clients.get(ws);if(!user){send(ws,{type:'error',message:'Сначала укажите имя'});return;}
   if(d.type==='typing'){clearTimeout(typingTimer);broadcast({type:'typing',id:user.id,name:user.name,isTyping:d.isTyping===true},ws);if(d.isTyping===true)typingTimer=setTimeout(()=>broadcast({type:'typing',id:user.id,name:user.name,isTyping:false},ws),2000);return;}
   if(d.type==='message'){
    const text=typeof d.message==='string'?d.message.trim():'';if(!text||text.length>2000){send(ws,{type:'error',message:'Сообщение: 1–2000 символов'});return;}
    const message={type:'message',id:randomUUID(),senderId:user.id,sender:user.name,message:text,timestamp:new Date().toISOString()};history.push(message);if(history.length>50)history.shift();broadcast(message);broadcast({type:'typing',id:user.id,name:user.name,isTyping:false},ws);
   }
  });
  ws.on('error',()=>{});ws.on('close',()=>{clearTimeout(typingTimer);const user=clients.get(ws);clients.delete(ws);if(user){broadcast({type:'typing',id:user.id,name:user.name,isTyping:false});system(`Пользователь ${user.name} покинул чат`);users();}});
 });
 const timer=setInterval(()=>{for(const ws of wss.clients){if(!ws.alive){ws.terminate();continue;}ws.alive=false;ws.ping();}},30000);timer.unref();
 return {server,wss,listen:()=>new Promise(resolve=>server.listen(port,host,resolve)),close:()=>new Promise(resolve=>{clearInterval(timer);for(const ws of wss.clients)ws.terminate();wss.close(()=>server.close(resolve));})};
}
if(require.main===module){const app=createChat({port:Number(process.env.PORT||8080),host:process.env.HOST||'127.0.0.1'});app.listen().then(()=>console.log(`Чат: http://127.0.0.1:${process.env.PORT||8080}`));}
module.exports={createChat};
