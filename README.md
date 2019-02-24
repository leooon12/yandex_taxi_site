# Laravel-Project Template with laradock

## Installing:
* clone project
* git submodule init 
* git submodule update

## Launching:
* enter the laradock folder and rename env-example to .env: cp env-example .env
* run containers: sudo docker-compose up -d apache2 postgres

## List current running Containers:
* docker-compose ps

## Close running containers:
### All:
* docker-compose stop
### Single:
* docker-compose stop {container-name}

## Delete all existing Containers:
* docker-compose down

## Enter in the container:
* docker-compose exec {container-name} bash

## Build and rebuild container:
* docker-compose build {container-name}
