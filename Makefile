COMPOSE_FILES=./docker-compose.yml
POSTGRES_COMPOSE_FILES=./.docker/docker-compose-postgres.yml
ELASTIC_COMPOSE_FILES=./.docker/elk/docker-compose-elk.yml

help:

	@echo "build"
	@echo "  Build docker image and start containers."
	@echo ""
	@echo "up"
	@echo "  Create and start containers."
	@echo ""
	@echo "up-graylog"
	@echo "  Create and up graylog"
	@echo ""
	@echo "recreate"
	@echo " Force to recreate containers"
	@echo ""
	@echo "restart"
	@echo "  Restart containers."
	@echo ""
	@echo "status"
	@echo "  Shows the status of the current containers."
	@echo ""
	@echo "shell"
	@echo "  Starting a zsh shell as \"www-data\" user in web container."
	@echo ""
	@echo "shell-as-root"
	@echo "  Starting a bash shell as \"root\" user in web container."
	@echo ""
	@echo "destroy"
	@echo "  Stop and remove containers, networks, images, and volumes."
	@echo ""
	@echo "pull-git"
	@echo "  Pull git and submodule"
	@echo ""
	@echo "submodules"
	@echo "  Pull and update  submodules"
	@echo ""
	@echo "destroy"
	@echo "  Stop and remove containers, networks, images, and volumes."
	@echo ""
	@echo "submodule-develop"
	@echo "  Checkout all submodule to develop branch"
	@echo ""
	@echo "submodule-master"
	@echo "  Checkout all submodule to master branch"
	@echo ""

build:
	docker compose -f $(COMPOSE_FILES) build --no-cache

up:
	docker compose -f $(COMPOSE_FILES) -f $(POSTGRES_COMPOSE_FILES) -f $(ELASTIC_COMPOSE_FILES) up -d app redis postgres pgadmin elasticsearch

down:
	docker compose -f $(COMPOSE_FILES) -f $(POSTGRES_COMPOSE_FILES) down

up-graylog:
	docker compose -f $(COMPOSE_FILES) -f $(GRAYLOG_COMPOSE_FILES) up -d

restart:
	docker compose -f $(COMPOSE_FILES) restart

recreate:
	docker compose -f $(COMPOSE_FILES) up -d --force-recreate

status:
	docker compose -f $(COMPOSE_FILES) ps

destroy:
	docker compose -f $(COMPOSE_FILES) down

shell:
	docker compose -f $(COMPOSE_FILES) exec --user=www-data app ash

shell-as-root:
	docker compose -f $(COMPOSE_FILES) exec  --user=root app ash

git:
	git pull --recurse-submodules

submodules:
	git submodule update --remote

submodule-develop:
	git submodule foreach --recursive 'git checkout develop'

submodule-develop-rebase:
	git submodule foreach --recursive 'git pull --rebase origin develop'

submodule-master:
	git submodule foreach --recursive 'git checkout master'
