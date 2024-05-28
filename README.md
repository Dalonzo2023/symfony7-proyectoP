# Pokedex

## 1. Crear y borrar base de datos
```console
php bin/console doctrine:database:drop --force
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
```
-drop->Borras la base de datos
-create->Creas la base de datos
-migrate->Introduces las tablas

## Endpoinst (direcciones)
-localhost:8000/categorias/insertar
-localhost:8000/categorias/insertar/Semilla
-localhost:8000/categorias/insertar-array
