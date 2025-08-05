#/bin/bash

. ./.env

php_wait() {
    until [ "$res" = "999" ]
    do
        echo "$res"
        res=$(docker exec $PREFIX-$PHP_CONTAINER echo 999)
        sleep 1
    done
}

db_wait() {
    until [ "$res" = "999" ]
    do
        echo "$res"
        res=$(docker exec "$PREFIX-$DB_CONTAINER" echo 999)
        sleep 1
    done
}

docker_build() {
    docker compose --project-directory ./ -f ./docker/docker-compose.yml build
}

docker_up() {
    docker compose --project-directory ./ -f ./docker/docker-compose.yml up -d
}

docker_down() {
    docker compose --project-directory ./ -f ./docker/docker-compose.yml down
}

docker_ps() {
    docker compose --project-directory ./ -f ./docker/docker-compose.yml ps
}

docker_exec() {
    docker compose --project-directory ./ -f ./docker/docker-compose.yml exec
}

build() {
    docker_build
}

start() {
    docker_up
    sleep 1
    php_wait
    sleep 1
    require
    sleep 1
}

stop() {
    docker_down
}

require() {
    docker exec "$PREFIX-$PHP_CONTAINER" composer install
}

update() {
    docker exec "$PREFIX-$PHP_CONTAINER" composer update
}

migrate() {
    docker exec "$PREFIX-$PHP_CONTAINER" /app/bin/app.php migrate
}

import() {
    docker exec "$PREFIX-$PHP_CONTAINER" /app/bin/app.php import
}

test() {
    docker exec "$PREFIX-$PHP_CONTAINER" vendor/phpunit/phpunit/phpunit
}

init () {
    build
    sleep 1
    start
    sleep 1
    migrate
    sleep 1
    import
    sleep 1
}

status() {
    docker_ps
}

ready_message() {
    echo -e "\nService \033[32m\"$APP_NAME\"\033[0m avaliable on url \033[32mhttp://localhost:$NGINX_EXT_PORT\033[0m\n"
}

get_version() {
    IS_DIG='^([[:digit:]])([.]{1,}[[:digit:]]+)?$'
    IS_WSL="wsl"

    # елсли указан номер версии, используем его
    if [[ $COMPOSER_VERSION =~ $IS_DIG ]]; then
        COMPOSER_VERSION=$COMPOSER_VERSION
        export COMPOSER_VERSION=$COMPOSER_VERSION
    # если указан wsl, используем версию 3.3
    elif [[ $COMPOSER_VERSION == $IS_WSL ]]; then
        COMPOSER_VERSION=3.3
        export COMPOSER_VERSION=3.3
    # в остальных случаях используем последнюю версию 3.9
    else
        COMPOSER_VERSION=3.9
        export COMPOSER_VERSION=3.9
    fi
}

check_version() {
    check_min=$(echo "scale=0; $COMPOSER_VERSION/3" | bc)
    check_max=$(echo "scale=0; $COMPOSER_VERSION/4" | bc)

    if [[ $check_min -eq 0 ]]; then
        echo -e "Error: The minimum version of docker-compose.yml must be greater than or equal to 3, $COMPOSER_VERSION given\n"
        exit
    elif [[ $check_max -ne 0 ]]; then
        echo -e "Error: The minimum version of docker-compose.yml should be less than 4, $COMPOSER_VERSION given\n"
        exit
    fi
}

usage_message() {
    echo -e "Usage: app.sh [\033[3minit|start|stop|status|migrate|require|update\033[0m]"
    help
    echo "Starting app.sh for the first time may take a few minutes"
    echo
}

help()
{
    echo
    echo "options:"
    echo -e "  init       Install application"
    echo -e "  start      Start installed application"
    echo -e "  stop       Stop running application"
    echo -e "  status     Show status of application containers"
    echo -e "  migrate    Runs command ${bold}bin/app.php migrate ${normal} into ${bold}php${normal} container"
    echo -e "  import     Runs command ${bold}bin/app.php import ${normal} into ${bold}php${normal} container"
    echo -e "  require    Runs command ${bold}composer install${normal} into ${bold}php${normal} container"
    echo -e "  update    Runs command ${bold}composer update${normal} into ${bold}php${normal} container"
    echo
}

COMMAND=$1
PARAM=$2

get_version
check_version

case "$1" in
    start)
        start
        ready_message
        ;;
    stop)
        stop
        ;;
    init)
        init
        ready_message
        ;;
    require)
        require
        ;;
    update)
        update
        ;;
    migrate)
        migrate
        ;;
    import)
        import
        ;;
    status)
        status
        ;;
    test)
        test
        ;;
    *)
        usage_message
    ;;
esac
