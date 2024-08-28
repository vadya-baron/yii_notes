# Название папки сервиса
SERVICE_NAME=notes

# Путь до сервиса
PATH_TO_SERVICE=engines/

# Docker
dc=docker compose
d=docker
df=docker-compose.yml
ba=exec -it client-sites_php8 bash
bar=exec -u 0 -it client-sites_php8 bash

# Переменные для работы с сервисом
name?=null
site_id?=null
type?=null
fresh?=null

# Данные для работы Make
PHONY = help, migrate, create-migrate, test, composer, composer-update, composer-dump, yii

help:
	@echo "----------------------------------------------HELP----------------------------------------------"
	@echo "Накатить миграции	  								-> make migrate"
	@echo "Создать Миграцию	(create-migrate name=table_name)	-> make create-migrate name=table_name"
	@echo "Пройтись по тестам									-> make test"
	@echo "Composer Приложение (команды заключать в '')	  		-> make composer"
	@echo "Composer update	  									-> make composer-update"
	@echo "Composer dump	  									-> make composer-dump"
	@echo "Yii Приложение (команды заключать в '')	  			-> make yii"
	@echo "------------------------------------------------------------------------------------------------"


migrate:
	${d} ${ba} -c "cd $(PATH_TO_SERVICE)$(SERVICE_NAME)/application && php yii migrate"

create-migrate:
ifeq ($(name), null)
	@echo "Укажите имя миграции: make create-migrate name=table_name"
else
	${d} ${ba} -c "cd $(PATH_TO_SERVICE)$(SERVICE_NAME)/application && php yii migrate/create create_$(name)_table"
endif

composer:
	${d} ${ba} -c "cd $(PATH_TO_SERVICE)$(SERVICE_NAME)/application && composer $(filter-out $@,$(MAKECMDGOALS))"

composer-update:
	${d} ${ba} -c "cd $(PATH_TO_SERVICE)$(SERVICE_NAME)/application && composer update"

composer-dump:
	${d} ${ba} -c "cd $(PATH_TO_SERVICE)$(SERVICE_NAME)/application && composer dump-autoload"


yii:
	${d} ${ba} -c "cd $(PATH_TO_SERVICE)$(SERVICE_NAME)/application && php yii $(filter-out $@,$(MAKECMDGOALS))"


