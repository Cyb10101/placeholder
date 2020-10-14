#!/usr/bin/env bash

SCRIPTPATH="$( cd "$(dirname "$0")" >/dev/null 2>&1 ; pwd -P )"
cd "${SCRIPTPATH}"

APPLICATION_UID=${APPLICATION_UID:-1000}
APPLICATION_GID=${APPLICATION_GID:-1000}
APPLICATION_USER=${APPLICATION_USER:-application}
APPLICATION_GROUP=${APPLICATION_GROUP:-application}

loadEnvironmentVariables() {
    if [ -f ".env" ]; then
      source .env
    fi
    if [ -f ".env.local" ]; then
      source .env.local
    fi
}

setDockerComposeFile() {
    DOCKER_COMPOSE_FILE=docker-compose.yml

    # Symfony
    APP_ENV=${APP_ENV:-}
    if [ "${APP_ENV}" == "dev" ]; then
        DOCKER_COMPOSE_FILE=docker-compose.dev.yml
    fi

    # TYPO3
    TYPO3_CONTEXT=${TYPO3_CONTEXT:-}
    if [ "${TYPO3_CONTEXT:0:11}" == "Development" ]; then
        DOCKER_COMPOSE_FILE=docker-compose.dev.yml
    fi

    # Custom
    ENV_DOCKER_CONTEXT=${ENV_DOCKER_CONTEXT:-}
    if [ "${ENV_DOCKER_CONTEXT:0:11}" == "Development" ]; then
        DOCKER_COMPOSE_FILE=docker-compose.dev.yml
    fi
}

dockerComposeCmd() {
    docker-compose -f "${DOCKER_COMPOSE_FILE}" "${@:1}"
}

checkRoot() {
    if [[ $EUID -ne 0 ]]; then
        echo 'You must be a root user!' 2>&1
        exit 1
    fi
}

checkGitMaster() {
    if [[ $(git symbolic-ref --short -q HEAD) != 'master' ]]; then
        echo 'ERROR: Git is not on branch master!'
        [[ "$0" = "$BASH_SOURCE" ]] && exit 1 || return 1 # handle exits from shell or function but don't exit interactive shell
    fi
}

checkGit() {
    if [[ $(git diff --stat) != '' ]]; then
        echo
        git status --porcelain
        echo

        read -p 'Git is dirty... Continue? [y/N] ' -n 1 -r
        echo
        if [[ ! $REPLY =~ ^[Yy]$ ]]; then
            [[ "$0" = "$BASH_SOURCE" ]] && exit 1 || return 1 # handle exits from shell or function but don't exit interactive shell
        fi
    fi
}

setPermissions() {
    chown -R ${APPLICATION_UID}:${APPLICATION_GID} .
    find . -type d -exec chmod ugo+rx,ug+w {} \;
    find . -type f -exec chmod ugo+r,ug+w {} \;
}

runDeploy() {
    git pull origin master
    setPermissions

    # Git: Deploy as user in container (SSH-Key for private repositories needed)
    #startFunction exec-web git pull origin master

    # Deploy as user in container
    startFunction start
    startFunction exec-web composer install

    # Update database schema
    read -p 'Update database schema? [y/N] ' -n 1 -r
    echo
    if [[ $REPLY =~ ^[Yy]$ ]]; then
        startFunction exec-web /app/bin/console doctrine:schema:update --force
    fi

    # Clear cache
    startFunction exec-web /app/bin/console cache:clear --no-warmup
    startFunction exec-web /app/bin/console cache:warmup
}

loadEnvironmentVariables
setDockerComposeFile

startFunction() {
    case ${1} in
        start)
            startFunction pull && \
            startFunction build && \
            startFunction up
        ;;
        up)
            dockerComposeCmd up -d
        ;;
        down)
            dockerComposeCmd down --remove-orphans
        ;;
        login-root)
            dockerComposeCmd exec web bash
        ;;
        login)
            startFunction bash
        ;;
        bash)
            dockerComposeCmd exec -u ${APPLICATION_USER} web bash
        ;;
        zsh)
            dockerComposeCmd exec -u ${APPLICATION_USER} web zsh
        ;;
        exec-web)
            dockerComposeCmd exec -u ${APPLICATION_USER} web "${@:2}"
        ;;
        deploy)
            checkRoot
            checkGitMaster
            checkGit
            runDeploy
        ;;
        *)
            dockerComposeCmd "${@:1}"
        ;;
    esac
}

startFunction "${@:1}"
exit $?
