#!/usr/bin/env bash

loadEnvironmentVariables() {
    if [ -f ".env" ]; then
      source .env
    fi
    if [ -f ".env.local" ]; then
      source .env.local
    fi
}

gitCheckBranch() {
    if [ -d ".git" ]; then
        if [[ $(git symbolic-ref --short -q HEAD) != "${1}" ]]; then
            echo "ERROR: Git is not on branch ${1}!"
            [[ "$0" = "$BASH_SOURCE" ]] && exit 1 || return 1 # handle exits from shell or function but don't exit interactive shell
        fi
    fi
}

gitCheckDirty() {
    if [ -d ".git" ]; then
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
    fi
}

gitPull() {
    if [ -d ".git" ]; then
        git pull "${@:1}"
    fi
}

composerInstall() {
    if [ -f "composer.json" ]; then
        ${BIN_PHP} ${BIN_COMPOSER} install "${@:1}"
    fi
}

symfonyUpdateDatabase() {
    if [ -f "symfony.lock" ]; then
        read -p 'Update database schema? [y/N] ' -n 1 -r
        echo
        if [[ $REPLY =~ ^[Yy]$ ]]; then
            ${BIN_PHP} ./bin/console doctrine:schema:update --force
        fi
    fi
}

symfonyClearCache() {
    if [ -f "symfony.lock" ]; then
        ${BIN_PHP} ./bin/console cache:clear --no-warmup
        ${BIN_PHP} ./bin/console cache:warmup
    fi
}

loadEnvironmentVariables
GIT_BRANCH="${GIT_BRANCH:-master}"
BIN_PHP="${BIN_PHP:-php}"
BIN_COMPOSER="${BIN_COMPOSER:-composer}"

gitCheckBranch ${GIT_BRANCH}
gitCheckDirty

gitPull origin ${GIT_BRANCH}
composerInstall

symfonyUpdateDatabase
symfonyClearCache
