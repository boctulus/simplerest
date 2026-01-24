# Fix performance

https://chatgpt.com/c/6974c636-a474-8324-8da1-8a09ec041db5

- El ApiController deberia poder funcionar con el web_router para poder apagar el front_controller (es mas lento??)

```
'web_router'       => true,
'console_router'   => true,
'front_controller' => true,
```

- El uso del ACL se deberia poder apagar por request / por endpoint / por pipeline.

- Poder deshabilitar es escaneo de modulos en produccion

